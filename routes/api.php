<?php
// routes/api.php — GANTI SELURUH FILE INI

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DestinationController;
use App\Http\Controllers\Api\SavedController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\Admin\AdminDestinationController;
use App\Http\Controllers\Api\Admin\AdminBookingController;
use App\Http\Controllers\Api\Admin\AdminScheduleController;

// ── Public ───────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
});

// ── Auth required ─────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',      [AuthController::class, 'me']);
    });

    // Destinations (user)
    Route::get('/destinations/featured', [DestinationController::class, 'featured']);
    Route::get('/destinations',          [DestinationController::class, 'index']);
    Route::get('/destinations/{id}',     [DestinationController::class, 'show']);

    // Saved
    Route::get('/saved',          [SavedController::class, 'index']);
    Route::post('/saved/toggle',  [SavedController::class, 'toggle']);

    // Bookings (user)
    Route::get('/bookings',              [BookingController::class, 'index']);
    Route::post('/bookings',             [BookingController::class, 'store']);
    Route::get('/bookings/{id}',         [BookingController::class, 'show']);
    Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel']);

    // Schedules (user — lihat slot & book dari slot)
    Route::get('/schedules',                  [BookingController::class, 'getSchedules']); // delegated
    Route::post('/schedules/{id}/book',       [BookingController::class, 'bookSchedule']);

    // Wallet (E-Wallet only)
    Route::prefix('wallet')->group(function () {
        Route::get('/',              [WalletController::class, 'show']);
        Route::get('/transactions',  [WalletController::class, 'transactions']);
        Route::post('/topup',        [WalletController::class, 'topup']);
    });

    // ── Admin (is_admin = true) ───────────────
    Route::prefix('admin')->middleware('admin')->group(function () {

        // CRUD Destinations
        Route::get('/destinations',                          [DestinationController::class, 'index']);
        Route::post('/destinations',                         [DestinationController::class, 'store']);
        Route::get('/destinations/{id}',                     [DestinationController::class, 'show']);
        Route::put('/destinations/{id}',                     [DestinationController::class, 'update']);
        Route::delete('/destinations/{id}',                  [DestinationController::class, 'destroy']);
        Route::post('/destinations/{id}/featured',           [DestinationController::class, 'toggleFeatured']);

        // CRUD Bookings
        Route::get('/bookings',          [AdminBookingController::class, 'index']);
        Route::put('/bookings/{id}',     [AdminBookingController::class, 'update']);
        Route::delete('/bookings/{id}',  [AdminBookingController::class, 'destroy']);

        // CRUD Schedules / Slots
        Route::get('/schedules',         [AdminScheduleController::class, 'index']);
        Route::post('/schedules',        [AdminScheduleController::class, 'store']);
        Route::put('/schedules/{id}',    [AdminScheduleController::class, 'update']);
        Route::delete('/schedules/{id}', [AdminScheduleController::class, 'destroy']);
    });
});
