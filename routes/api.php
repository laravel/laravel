<?php

use App\Http\Controllers\Api\FonnteController;
use App\Http\Controllers\Api\PaymentCallbackController;
use Illuminate\Support\Facades\Route;

Route::post('/fonnte/webhook', [FonnteController::class, 'webhook']);
Route::post('/payment/callback', [PaymentCallbackController::class, 'handle']);

Route::get('/health', function () {
    return response()->json([
        'status' => 'ArcanePay API is running',
        'version' => '1.0.0',
        'timestamp' => now()->toIso8601String(),
    ]);
});

Route::get('/games', function () {
    $categories = App\Models\Category::where('status', true)
        ->with(['products' => fn ($q) => $q->where('status', true)])
        ->get();
    return response()->json($categories);
});
