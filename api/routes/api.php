<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OTPCodeController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::controller(AuthController::class)->prefix('auth')->group(function () {
        Route::post('login', 'login')->withoutMiddleware('auth:api');
        Route::post('me', 'me');
        Route::post('refresh', 'refresh');
        Route::post('logout', 'logout');
    });

    Route::controller(OTPCodeController::class)->prefix('otp-code')->withoutMiddleware('auth:api')->group(function () {
        Route::post('generate', 'generate');
        Route::post('verify', 'verify');
    });

    Route::controller(PasswordController::class)->prefix('password')->group(function () {
        Route::post('reset', 'reset');
        Route::post('change', 'change');
    });

    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::patch('/{user}', 'update');
        Route::delete('/{user}', 'destroy');
    });

    Route::controller(RoleController::class)->prefix('roles')->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::patch('/{user}', 'update');
        Route::delete('/{user}', 'destroy');
    });

});
