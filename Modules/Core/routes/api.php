<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\CoreController;

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

Route::prefix('v1')->group(function () {
    Route::apiResource('core', CoreController::class)->names('core');
});

Route::prefix(config('config.app_version'))->group(function () {
    Route::prefix('config')->group(function () {
        Route::get('countries', [CoreController::class, 'index']);
        Route::get('counties', [CoreController::class, 'counties']);
        Route::get('industries', [CoreController::class, 'industries']);
    });
});
