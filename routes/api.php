<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

Route::post('/chat/{agent:slug}/guest', [ChatController::class, 'guest']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/chat/{agent:slug}/send', [ChatController::class, 'send']);
});
