<?php

namespace Modules\OrderManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\OrderManagement\Http\Requests\CreateOrderRequest;
use Modules\OrderManagement\Models\OrderManager;
use Modules\OrderManagement\Services\OrderManagementService;
use Modules\OrderManagement\Services\StripeWebhookService;

class OrderManagementController extends Controller
{
    protected $orderService;
    protected $webhookService;
    public function __construct(OrderManagementService $orderService, StripeWebhookService $webhookService)
    {
        $this->orderService = $orderService;
        $this->webhookService = $webhookService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('ordermanagement::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createOrder(CreateOrderRequest $request)
    {
        return $this->orderService->createOrder($request);
    }


    public function cancelOrder($orderId)
    {
        return $this->orderService->cancelOrder($orderId);
    }

    public function receiveWebhook(Request $request)
    {
        return $this->webhookService->receiveStripeWebhook($request);
    }
}
