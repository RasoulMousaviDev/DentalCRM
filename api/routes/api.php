<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->middleware('auth:api')->prefix('auth')->group(function () {
    Route::post('login', 'login')->withoutMiddleware('auth:api');
    Route::post('me', 'me');
    Route::post('refresh', 'refresh');
    Route::post('logout', 'logout');
});
