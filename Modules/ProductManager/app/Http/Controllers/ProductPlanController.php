<?php

namespace Modules\ProductManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ProductManager\Http\Requests\CreateProductPlanRequest;
use Modules\ProductManager\Http\Requests\ProductPlanPromotionRequest;
use Modules\ProductManager\Http\Requests\UpdateProductPlanRequest;
use Modules\ProductManager\Services\ProductPlanService;

class ProductPlanController extends Controller
{
    protected $service;
    public function __construct(ProductPlanService $service)
    {
        $this->service = $service;
    }



    /**
     * Get products
     */
    public function productPlans(Request $request)
    {
        return $this->service->productPlans($request);
    }

    /**
     * Create product plan
     */
    public function createProductPlan(CreateProductPlanRequest $request)
    {
        return $this->service->createProductPlan($request);
    }

    /**
     * Update product plans
     */
    public function updateProductPlan(UpdateProductPlanRequest $request)
    {
        return $this->service->updateProductPlan($request);
    }

    /**
     * Create product plan
     */
    public function createProductPlanPromotion(ProductPlanPromotionRequest $request)
    {
        return $this->service->createProductPlanPromotion($request);
    }
}
