<?php

use Illuminate\Support\Facades\Route;
use Modules\UserProfile\Http\Controllers\NotificationSetupController;
use Modules\UserProfile\Http\Controllers\PaymentOptionController;
use Modules\UserProfile\Http\Controllers\UserProfileController;

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
    Route::apiResource('userprofile', UserProfileController::class)->names('userprofile');
});


Route::prefix(config('config.app_version'))->group(function () {
//Profile updates
    Route::prefix('profile')->group(function () {
        Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'update'], function () {
            Route::post('details', [UserProfileController::class, 'updatePersonalDetails']);
            Route::get('details/view', [UserProfileController::class, 'getProfileData']);
            Route::post('password', [UserProfileController::class, 'updatePassword']);
            Route::post('image', [UserProfileController::class, 'uploadProfilePicture']);
            Route::get('2fa', [UserProfileController::class, 'enable2FA']);
            Route::get('disable/2fa', [UserProfileController::class, 'disable2FA']);
            Route::get('view', [UserProfileController::class, 'viewQRCode']);
        });
    });

    // Payment option
    Route::prefix('payment')->group(function () {
        Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'options'], function () {
            Route::post('', [PaymentOptionController::class, 'addPaymentOption']);
            Route::post('verify/phone', [PaymentOptionController::class, 'verifyMobilePaymentPhoneNumber']);
            Route::get('', [PaymentOptionController::class, 'getAddedOptions']);
        });
    });


    // Notification set up
    Route::prefix('notifications')->group(function () {
        Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'set'], function () {
            Route::post('', [NotificationSetupController::class, 'createNotification']);
        });
    });
});
