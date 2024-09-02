<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware(\App\Http\Middleware\ApiKeyMiddleware::class)->group(function () {
    Route::apiResource('cities', \App\Http\Controllers\API\CityController::class)->only(['index', 'show']);
    Route::apiResource('vehicles', \App\Http\Controllers\API\VehicleController::class)->only(['index', 'show']);
    Route::post('calculatePrice', [\App\Http\Controllers\API\PriceController::class, 'calculateDistance'])->name('calculatePrice');
});
