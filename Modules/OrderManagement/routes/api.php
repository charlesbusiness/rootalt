<?php

use Illuminate\Support\Facades\Route;
use Modules\OrderManagement\Http\Controllers\OrderManagementController;

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

Route::prefix(config('config.app_version'))->group(
    function () {
        Route::prefix('orders')->group(function () {
            Route::post('webhook', [OrderManagementController::class, 'receiveWebhook']);
            Route::group(['middleware' => ['auth:sanctum'],], function () {
                Route::post('', [OrderManagementController::class, 'createOrder']);
                Route::post('cancel', [OrderManagementController::class, 'cancelOrder']);
            });
        });
    }
);
