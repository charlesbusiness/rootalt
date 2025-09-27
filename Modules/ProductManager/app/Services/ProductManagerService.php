<?php

namespace Modules\ProductManager\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Upload;
use Modules\Core\Services\CoreService;
use Modules\ProductManager\Http\Requests\CreateProductCategoryRequest;
use Modules\ProductManager\Http\Requests\CreateProductRequest;
use Modules\ProductManager\Http\Requests\UpdateProductCategoryRequest;
use Modules\ProductManager\Http\Requests\UpdateProductRequest;
use Modules\ProductManager\Models\Product;
use Modules\ProductManager\Models\ProductCategory;
use Modules\ProductManager\Models\ProductMovement;
use Throwable;

class ProductManagerService extends CoreService
{
    protected $productModel;
    protected $productCatModel;
    protected $user;
    protected $upload;
    protected $productMovementModel;

    public function __construct(
        User $user,
        Product $product,
        Upload $upload,
        ProductCategory $productCatModel,
        ProductMovement $productMovementModel
    ) {
        $this->productCatModel = $productCatModel;
        $this->productModel = $product;
        $this->user = $user;
        $this->upload = $upload;
        $this->productMovementModel = $productMovementModel;
        parent::__construct();
    }

    /** **
     * Allow admin to create product categories
     ** */
    public function createCategory(CreateProductCategoryRequest $request)
    {
        try {
            $data = $request->validated();
            $createdData = $this->productCatModel->updateOrCreate(
                ['category_name' => $data['category_name']],
                $data
            );
            $this->message = "Category created";
            return successfulResponse($createdData, $this->message);
        } catch (Throwable $e) {

            logError($e);
            return failedResponse(null, $this->errorMessage);
        }
    }

    /** **
     * Allow admin to update product categories
     ** */
    public function updateCategory(UpdateProductCategoryRequest $request)
    {
        try {
            $cat = $this->productCatModel->find($request->id);

            $cat->update(
                [
                    'category_name' => $request->category_name,
                    'category_description' => $request->category_description,
                ],

            );
            $cat->refresh();
            $this->message = "Category updated";
            return successfulResponse($cat, $this->message);
        } catch (Throwable $e) {

            logError($e);
            return failedResponse(null, $this->errorMessage);
        }
    }

    public function getCategories()
    {
        try {
            $data = $this->productCatModel
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
    public function createProduct(CreateProductRequest $request)
    {
        DB::beginTransaction();
        try {

            $data = $request->validated();
            unset($data['product_photo']);

            $createdData = $this->productModel->updateOrCreate(
                ['product_sku' => $data['product_sku']],
                $data
            );


            $uploadData = $this->uploadMultipleFiles($request, 'product_photo');

            foreach ($uploadData as $upload) {
                $this->upload->create([
                    'upload_type' => $this->productModel->uploadType,
                    'upload_path' => $upload['url'],
                    'file_size' => $upload['size'],
                    'file_name' => $upload['fileName'],
                    'entity_id' => $createdData->id,
                ]);
            }
            DB::commit();
            $this->message = "Product Create created";
            return successfulResponse($createdData, $this->message);
        } catch (Throwable $e) {

            DB::rollBack();
            logError($e);
            return failedResponse(null, $this->errorMessage);
        }
    }


    /** **
     * Allow admin to update product
     ** */


    public function updateProduct(UpdateProductRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            $product = $this->productModel->query()
                ->where('id', $data['id'])
                ->lockForUpdate()
                ->firstOrFail();

            $movement = $this->productMovementModel->makeMovement(
                $product,
                $data['movement_type'],
                (int) $data['product_qty'],
                $data['reason'] ?? 'Manual adjustment'
            );

            // Apply stock change
            if ($movement->transaction_type === 'inbound') {
                $product->increment('product_qty', $movement->qty);
            } else {
                if ($product->product_qty < $movement->qty) {
                    return failedResponse($product, 'Trying to reduce more than available stock', 422);
                }
                $product->decrement('product_qty', $movement->qty);
            }

            // Handle product photos
            if ($request->hasFile('product_photo')) {
                $associatedUpload = $this->upload
                    ->query()
                    ->where('entity_id', $product->id)
                    ->where('upload_type', $this->productModel->uploadType)
                    ->get();

                foreach ($associatedUpload as $uploadedFile) {
                    $this->deleteFiles($uploadedFile->upload_path);
                    $uploadedFile->delete();
                }

                $uploadData = $this->uploadMultipleFiles($request, 'product_photo');
                foreach ($uploadData as $upload) {
                    $this->upload->create([
                        'upload_path' => $upload['url'],
                        'file_size' => $upload['size'],
                        'file_name' => $upload['fileName'],
                        'upload_type' => $this->productModel->uploadType,
                        'entity_id' => $product->id,
                    ]);
                }
            }

            DB::commit();
            $product->refresh();
            $this->message = "Product updated";
            return successfulResponse($product, $this->message);
        } catch (Throwable $e) {
            DB::rollBack();
            logError($e);
            return failedResponse(null, $this->errorMessage);
        }
    }

    public function products(Request $request)
    {
        try {

            $products = $this->productModel->query()
                ->with(['uploads']);
            $requestHasPerPage = $request->filled('per_page');
            $data = $requestHasPerPage ? transformPaginatedData($products->paginate($request->per_page)) : ['data' => $products->get()];
            return count($data['data']) ? successfulResponse($data) : failedResponse(null, 'No content found', Response::HTTP_OK);
        } catch (Throwable $e) {

            logError($e);
            return failedResponse(null, $this->errorMessage);
        }
    }
}
