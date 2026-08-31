<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HardwareController;
use App\Http\Controllers\NodeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\DisasterEventController;
use App\Http\Controllers\SystemStateController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\RfidLogController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Hardware placeholder routes
    Route::prefix('hardware')->group(function () {
        Route::post('/sensor-data', [HardwareController::class, 'receiveSensorData']);
        Route::post('/ble-scan', [HardwareController::class, 'receiveBleScan']);
        Route::post('/rfid-scan', [HardwareController::class, 'scan']);
        Route::post('/send-sms', [HardwareController::class, 'sendSms']);
    });

    // Node routes
    Route::apiResource('nodes', NodeController::class);

    // Zone routes
    Route::apiResource('zones', ZoneController::class);

    // Student routes
    Route::apiResource('students', StudentController::class);

    // RFID log routes
    Route::get('rfid-logs', [RfidLogController::class, 'index']);
    Route::get('rfid-logs/{rfidLog}', [RfidLogController::class, 'show']);

    // Disaster event routes
    Route::apiResource('disaster-events', DisasterEventController::class);

    // System state routes
    Route::get('/system-state', [SystemStateController::class, 'index']);
    Route::put('/system-state', [SystemStateController::class, 'update']);
});
