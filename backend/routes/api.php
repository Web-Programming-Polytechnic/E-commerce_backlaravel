<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\LocationController;

Route::group([
    'prefix' => 'auth',
    'middleware' => 'api'
], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});

Route::middleware(['api', 'auth:api'])->group(function () {
    // Deliveries
    Route::get('deliveries', [DeliveryController::class, 'index']);
    Route::get('deliveries/history', [DeliveryController::class, 'history']);
    Route::get('deliveries/{id}', [DeliveryController::class, 'show']);
    Route::patch('deliveries/{id}/status', [DeliveryController::class, 'updateStatus']);
    Route::post('deliveries/{id}/proof', [DeliveryController::class, 'submitProof']);

    // Location
    Route::post('location/update', [LocationController::class, 'update']);
});
