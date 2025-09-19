<?php

namespace Modules\ProductManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ProductManager\Http\Requests\CreateProductCategoryRequest;
use Modules\ProductManager\Http\Requests\CreateProductRequest;
use Modules\ProductManager\Http\Requests\UpdateProductCategoryRequest;
use Modules\ProductManager\Http\Requests\UpdateProductRequest;
use Modules\ProductManager\Services\ProductManagerService;

class ProductManagerController extends Controller
{
    protected $service;
    public function __construct(ProductManagerService $service)
    {
        $this->service = $service;
    }

    /**
     * Create product Category
     */
    public function createProductCategory(CreateProductCategoryRequest $request)
    {

        return $this->service->createCategory($request);
    }
    /**
     * Create product Category
     */
    public function updateProductCategory(UpdateProductCategoryRequest $request)
    {
        return $this->service->updateCategory($request);
    }

    /**
     * Get product Category
     */
    public function getCategories(Request $request)
    {
        return $this->service->getCategories($request);
    }

    /**
     * Get products
     */
    public function products(Request $request)
    {
        return $this->service->products($request);
    }

    /**
     * Create products
     */
    public function createProduct(CreateProductRequest $request)
    {
        return $this->service->createProduct($request);
    }

    /**
     * Create products
     */
    public function updateProduct(UpdateProductRequest $request)
    {
        return $this->service->updateProduct($request);
    }
}
