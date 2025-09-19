<?php

namespace Modules\ProductManager\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Core\Services\CoreService;
use Modules\ProductManager\Http\Requests\CreateProductPlanRequest;
use Modules\ProductManager\Http\Requests\ProductPlanPromotionRequest;
use Modules\ProductManager\Http\Requests\UpdateProductPlanRequest;
use Modules\ProductManager\Models\Product;
use Modules\ProductManager\Models\ProductPlan;
use Modules\ProductManager\Models\ProductPlanPromotion;
use Throwable;

class ProductPlanService extends CoreService
{
    protected $productModel;
    protected $productPlanPromo;
    protected $auth;
    protected $user;
    protected $configService;
    protected $productPlanModel;

    public function __construct(
        User $user,
        Product $product,
        ProductPlan $productPlanModel,
        ProductPlanPromotion $productPlanPromo
    ) {
        $this->productModel = $product;
        $this->productPlanPromo = $productPlanPromo;
        $this->productPlanModel = $productPlanModel;
        $this->user = $user;
        parent::__construct();
    }




    public function getProductPlans()
    {
        try {
            $data = $this->productPlanModel
                ->query()
                ->orderBy('id', 'desc')
                ->get();

            $this->message = "Query Ok";
            return successfulResponse($data, $this->message);
        } catch (Throwable $e) {

            logError($e);
            return failedResponse(null, $this->errorMessage);
        }
    }


    /** **
     * Allow admin to create product categories
     ** */
    public function createProductPlan(CreateProductPlanRequest $request)
    {
        try {

            $data = $request->validated();
            $createdData = null;
            if ($request->has('plan_id') && $request->filled('plan_id')) {
                $createdData = $this->productPlanModel->query()
                    ->where('id', $request->plan_id)
                    ->update($data);
                $this->message = "Product plan updated";
            } else {

                $createdData = $this->productPlanModel->updateOrCreate(
                    ['product_name' => $data['product_name']],
                    $data
                );
                $this->message = "Product plan created";
            }

            return successfulResponse($createdData, $this->message);
        } catch (Throwable $e) {

            logError($e);
            return failedResponse(null, $this->errorMessage);
        }
    }

    /** **
     * Allow admin to update product categories
     ** */
    public function updateProductPlan(UpdateProductPlanRequest $request)
    {
        try {
            $data = $request->validated();
            $product = $this->productPlanModel->query()
                ->where('id', $data['id'])
                ->first();

            $product->update($data);

            $product->refresh();
            $this->message = "Product plan updated";
            return successfulResponse($product, $this->message);
        } catch (Throwable $e) {

            logError($e);
            return failedResponse(null, $this->errorMessage);
        }
    }


    public function productPlans(Request $request)
    {
        try {
            $products = $this->productPlanModel->query();
            $requestHasPerPage = $request->filled('per_page');
            $data = $requestHasPerPage ? transformPaginatedData($products->paginate($request->per_page)) : ['data' => $products->get()];
            return count($data['data']) ? successfulResponse($data) : failedResponse(null, 'No content found', Response::HTTP_OK);
        } catch (Throwable $e) {
            logError($e);
            return failedResponse(null, $this->errorMessage);
        }
    }


    /** **
     * Allow admin to create product categories
     ** */
    public function createProductPlanPromotion(ProductPlanPromotionRequest $request)
    {
        try {
            $data = $request->validated();
            $createdData = null;

            if ($request->has('id') && $request->filled('id')) {
                $createdData = $this->productPlanPromo->query()
                    ->where('id', $request->plan_id)
                    ->update($data);
                $this->message = "Product plan promotion updated";
            } else {

                $createdData = $this->productPlanPromo->create($data);
                $this->message = "Product plan promotion created";
            }

            return successfulResponse($createdData, $this->message);
        } catch (Throwable $e) {

            logError($e);
            return failedResponse(null, $this->errorMessage);
        }
    }
}
