<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public Routes
Route::post('/bookings', [BookingController::class, 'store']);
Route::post('/contacts', [ContactController::class, 'store']);

// Admin Routes (Recommended to wrap in auth middleware in production)
Route::prefix('admin')->group(function () {
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::patch('/bookings/{id}', [BookingController::class, 'update']);
    Route::get('/contacts', [ContactController::class, 'index']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
