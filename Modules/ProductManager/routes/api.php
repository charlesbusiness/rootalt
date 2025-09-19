<?php

use Illuminate\Support\Facades\Route;
use Modules\ProductManager\Http\Controllers\ProductManagerController;
use Modules\ProductManager\Http\Controllers\ProductPlanController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('productmanager', ProductManagerController::class)->names('productmanager');
});



Route::prefix(config('config.app_version'))->group(function () {
    //Products
    Route::prefix('product')->group(function () {
        Route::group(['middleware' => ['auth:sanctum'],], function () {

            Route::post('category', [ProductManagerController::class, 'createProductCategory']);

            Route::patch('category', [ProductManagerController::class, 'updateProductCategory']);

            Route::get('category', [ProductManagerController::class, 'getCategories']);

            //Products
            Route::post('/', [ProductManagerController::class, 'createProduct']);

            Route::post('/update', [ProductManagerController::class, 'updateProduct']);

            Route::get('data', [ProductManagerController::class, 'products']);
        });
    });

    //Product Plans
    Route::prefix('product/plans')->group(function () {
        Route::group(['middleware' => ['auth:sanctum'],], function () {

            Route::post('/', [ProductPlanController::class, 'createProductPlan']);
            Route::patch('/', [ProductPlanController::class, 'updateProductPlan']);
            Route::get('', [ProductPlanController::class, 'productPlans']);
            Route::post('promo', [ProductPlanController::class, 'createProductPlanPromotion']);
        });
    });
});
