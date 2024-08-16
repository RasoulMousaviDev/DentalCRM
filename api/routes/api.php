<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OTPCodeController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->middleware('auth:api')->prefix('auth')->group(function () {
    Route::post('login', 'login')->withoutMiddleware('auth:api');
    Route::post('me', 'me');
    Route::post('refresh', 'refresh');
    Route::post('logout', 'logout');
});

Route::controller(OTPCodeController::class)->prefix('otp-code')->group(function () {
    Route::get('', 'generate');
    Route::post('', 'verify');
});