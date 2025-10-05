<?php

namespace Modules\OrderManagement\Services;

use Illuminate\Support\Facades\DB;
use Modules\Core\Services\CoreService;
use Modules\OrderManagement\Http\Requests\CreateOrderRequest;
use Modules\OrderManagement\Models\OrderDetail;
use Modules\OrderManagement\Models\OrderManager;
use Modules\ProductManager\Models\Product;
use Modules\ProductManager\Models\ProductMovement;
use Throwable;

class OrderManagementService extends CoreService
{
    protected $productModel;
    protected $user;
    protected $orderModel;
    protected $orderDetailModel;
    protected $productMovementModel;
    protected $stripeService;

    public function __construct(
        Product $product,
        OrderManager $orderModel,
        OrderDetail $orderDetailModel,
        ProductMovement $productMovementModel,
        StripeService $stripeService
    ) {
        $this->productModel = $product;
        $this->orderModel = $orderModel;
        $this->orderDetailModel = $orderDetailModel;
        $this->productMovementModel = $productMovementModel;
        $this->stripeService = $stripeService;
        parent::__construct();
    }


    public function createOrder(CreateOrderRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();

        try {
            $year = now()->year;

            // 1. Create order header
            $order = $this->orderModel::create([
                'buyer_id'        => $data['buyer_id'],
                'year'            => $year,
                'status'          => 'pending',
                'payment_status'  => 'pending',
                'shipping_status' => 'not_shipped',
            ]);

            $totalQty  = 0;
            $subtotal  = 0;

            // 2. Loop through items
            foreach ($data['items'] as $item) {
                $product = $this->productModel::lockForUpdate()->findOrFail($item['product_id']);
                $qty     = (int) $item['qty'];

                // Prevent overselling
                if ($product->product_qty < $qty) {
                    DB::rollBack();
                    return failedResponse(null, "Not enough stock for {$product->product_name}", 422);
                }

                $lineTotal = $qty * $product->price;

                // Create order detail
                $this->orderDetailModel::create([
                    'order_manager_id' => $order->id,
                    'product_id'       => $product->id,
                    'qty'              => $qty,
                    'cost_per_item'    => $product->price,
                    'line_total'       => $lineTotal,
                ]);

                // Update totals
                $totalQty += $qty;
                $subtotal += $lineTotal;
                $product->decrement('product_qty', $qty);
                $this->productMovementModel->makeMovement($product, 'outbound', $qty, "Order Placement");
            }

            // 3. Tax/shipping
            $tax          = $subtotal * 0.09; // Example: 8%
            $shippingCost = 10.00;
            $grandTotal   = $subtotal + $tax + $shippingCost;

            // 4. Update order header
            $paymentIntent = $this->stripeService->createPaymentIntent($grandTotal, $order, 'usd');

            $order->update([
                'total_qty'     => $totalQty,
                'subtotal'      => $subtotal,
                'tax'           => $tax,
                'shipping_cost' => $shippingCost,
                'grand_total'   => $grandTotal,
                'payment_intent_id' => $paymentIntent->id
            ]);


            DB::commit();

            $this->message = "Order placed successfully";
            $summary = $order->load('details.product', 'buyer', 'address');
            $summary['client_secret'] = $paymentIntent->client_secret;
            return successfulResponse($summary, $this->message);
        } catch (Throwable $e) {
            DB::rollBack();
            logError($e);
            return failedResponse(null, "Order placement failed");
        }
    }

    public function cancelOrder($orderId)
    {
        DB::beginTransaction();

        try {

            $order = $this->orderModel::with('details')->lockForUpdate()->findOrFail($orderId);

            if ($order->status === 'canceled') {
                return failedResponse(null, "Order already canceled", 422);
            }

            if ($order->status === 'shipped') {
                return failedResponse(null, "Shipped orders cannot be canceled", 422);
            }

            foreach ($order->details as $detail) {
                $product = $this->productModel::lockForUpdate()->findOrFail($detail->product_id);
                $product->increment('product_qty', $detail->qty);
                $this->productMovementModel->makeMovement($product, 'outbound', $detail->qty, "Order #{$order->id} canceled");
            }

            $order->update(['status' => 'canceled']);
            DB::commit();

            return successfulResponse($order->fresh('details.product'), "Order canceled and stock restored");
        } catch (Throwable $e) {
            DB::rollBack();
            logError($e);
            return failedResponse(null, "Order cancellation failed");
        }
    }
}
