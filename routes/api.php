<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DestinationController;
use App\Http\Controllers\Api\SavedController;
use App\Http\Controllers\Api\BookingController;

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    Route::get('/destinations/featured', [DestinationController::class, 'featured']);
    Route::get('/destinations', [DestinationController::class, 'index']);
    Route::get('/destinations/{id}', [DestinationController::class, 'show']);

    Route::get('/saved', [SavedController::class, 'index']);
    Route::post('/saved/toggle', [SavedController::class, 'toggle']);

    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);

    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel']);
});
