<?php

namespace Modules\OrderManagement\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\OrderManagement\Models\OrderManager;
use Modules\ProductManager\Models\ProductMovement;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class StripeService
{
    protected $productModel;
    protected $user;
    protected $orderModel;
    protected $orderDetailModel;
    protected $productMovementModel;

    public function __construct(OrderManager $orderModel, ProductMovement $productMovementModel)
    {
        Stripe::setApiKey(config('OrderManagement.stripe.secret'));
        $this->orderModel = $orderModel;
        $this->productMovementModel = $productMovementModel;
    }


    /**
     * Create a payment intent
     */
    public function createPaymentIntent($amount, $order, $currency = 'usd')
    {
        return PaymentIntent::create([
            'amount' => (int) ($amount * 100), // amount in cents
            'currency' => $currency,
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'buyer_id' => $order->buyer_id,
            ],
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);
    }

    public function markOrderAsPaid($paymentIntent)
    {
        $orderNumber = $paymentIntent->metadata->order_number ?? null;
        if ($orderNumber) {
            $order = $this->orderModel->query()->where('order_number', $orderNumber)->first();
            if ($order) {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed',
                    'transaction_id' => $paymentIntent->id,
                ]);
                Log::info("✅ Order {$order->id} marked as paid");
            }
        }
    }

    public function markOrderAsFailed($paymentIntent)
    {
        $orderNumber = $paymentIntent->metadata->order_number ?? null;
        if ($orderNumber) {
            $order = $this->orderModel->query()->where('order_number', $orderNumber)->first();
            if ($order) {
                $order->update([
                    'payment_status' => 'failed',
                    'status' => 'cancelled',
                ]);

                Log::warning("❌ Order {$order->id} marked as failed");
            }
        }
    }

    public function cancelOrder($paymentIntent)
    {
        $orderNumber = $paymentIntent->metadata->order_number ?? null;
        if (!$orderNumber) return;

        $order = $this->orderModel->query()
            ->with('details.product')
            ->where('order_number', $orderNumber)
            ->first();

        if ($order) {
            $order->update([
                'payment_status' => 'cancelled',
                'status' => 'cancelled'
            ]);

            foreach ($order->details as $detail) {
                $detail->product->increment('product_qty', $detail->qty);
                $this->productMovementModel->makeMovement(
                    $detail->product,
                    'inbound',
                    $detail->qty,
                    "Stock restored after order cancellation #{$order->order_number}"
                );
            }
        }
    }


    public function processRefund($charge)
    {
        // charges have a payment_intent property that links back
        $paymentIntentId = $charge->payment_intent ?? null;
        if (!$paymentIntentId) return;

        $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
        $orderNumber = $paymentIntent->metadata->order_number ?? null;

        if ($orderNumber) {
            $order = $this->orderModel->query()->where('order_number', $orderNumber)->first();
            if ($order) {
                $order->update([
                    'payment_status' => 'refunded',
                    'status' => 'refunded'
                ]);

                foreach ($order->details as $detail) {
                    $detail->product->increment('product_qty', $detail->qty);
                    $this->productMovementModel->makeMovement(
                        $detail->product,
                        'inbound',
                        $detail->qty,
                        "Stock restored after order refunded #{$order->order_number}"
                    );
                }

                Log::info("💸 Order {$order->id} refunded");
            }
        }
    }
}
