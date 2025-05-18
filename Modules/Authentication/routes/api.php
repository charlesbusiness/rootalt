<?php

use Illuminate\Support\Facades\Route;
use Modules\Authentication\Http\Controllers\AccountRecoveryController;
use Modules\Authentication\Http\Controllers\AuthenticationController;
use Modules\Authentication\Http\Controllers\GoogleAuthController;

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

Route::prefix(config('config.app_version'))->group(function () {
    Route::prefix('user')->group(function () {
        Route::post('register', [AuthenticationController::class, 'store']);
        Route::post('verify', [AuthenticationController::class, 'verify']);
        Route::post('resend/code', [AuthenticationController::class, 'resendEmailVerification']);
        

        Route::group(['middleware' => 'verify-email', 'prefix' => 'login'], function () {
            Route::post('', [AuthenticationController::class, 'login']);
            Route::post('verify/2fa', [AuthenticationController::class, 'verify2FA']);
        });
    });

    // Account recovery
    Route::prefix('account/recovery')->group(function () {
        Route::post('email', [AccountRecoveryController::class, 'sendPasswordResetEmail'])->middleware('verify-email');
        Route::post('verify/otp', [AccountRecoveryController::class, 'verifyPasswordOtp']);
        Route::post('reset/password', [AccountRecoveryController::class, 'resetPassword']);
    });


    Route::prefix('auth/google')->group(function () {
        Route::post('generate-token', [GoogleAuthController::class, 'googleLogin']);
    });
});
