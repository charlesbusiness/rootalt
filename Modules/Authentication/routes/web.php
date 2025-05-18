<?php

use Illuminate\Support\Facades\Route;
use Modules\Authentication\Http\Controllers\AuthenticationController;
use Modules\Authentication\Http\Controllers\GoogleAuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([], function () {
    Route::resource('authentication', AuthenticationController::class)->names('authentication');
});

Route::prefix('auth/google')->group(function () {
    Route::get('', [GoogleAuthController::class, 'redirectToGoogle']);
    Route::get('callback', [GoogleAuthController::class, 'handleGoogleCallback']);

    Route::get('generate-token', [GoogleAuthController::class, 'handleGoogleTokenGeneration']);
    Route::get('/redirect', [GoogleAuthController::class, 'index']);
});
