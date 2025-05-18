<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/sanctum/csrf-cookie', function (Request $request) {
    return response()->json(['csrf' => csrf_token()]);
});

Route::middleware('auth:sanctum')->get('api/v1/user', function (Request $request) {
    return $request->user();
});


