<?php

namespace Modules\UserProfile\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\UserProfile\Services\PaymentOptionService;

class PaymentOptionController extends Controller
{
    protected $paymentOptionService;

    public function __construct(PaymentOptionService $paymentOptionService)
    {
        $this->paymentOptionService = $paymentOptionService;
    }


    /**
     * Display a listing of the resource.
     */
    public function addPaymentOption(Request $request)
    {
        return $this->paymentOptionService->addPaymentOption($request);
    }


    public function verifyMobilePaymentPhoneNumber(Request $request)
    {
        return $this->paymentOptionService->verifyMobileMoneyPhoneNumner($request);
    }

    
    /**
     * Show the specified resources.
     */
    public function getAddedOptions(Request $request)
    {
        return $this->paymentOptionService->getAllUserPaymentOptions($request);
    }

    
}
