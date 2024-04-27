<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\ApiRoomController;
use App\Http\Controllers\Api\ApiCustomerController;
use App\Http\Controllers\Api\ApiStaffController;
use App\Http\Controllers\Api\ApiServiceController;


Route::get('/banner-list',[ApiController::class, 'showBanner']);
Route::post('/save-banner',[ApiController::class, 'saveBanner']);
Route::post('/update-banner',[ApiController::class, 'updateBanner']);
Route::delete('/delete-banner',[ApiController::class, 'deleteBanner']);

Route::get('/room-list',[ApiRoomController::class, 'showRoom']);
Route::post('/save-room',[ApiRoomController::class, 'saveRoom']);
Route::post('/update-room',[ApiRoomController::class, 'updateRoom']);
Route::delete('/delete-room',[ApiRoomController::class, 'deleteRoom']);


Route::get('/customer-list',[ApiCustomerController::class, 'showCustomer']);
Route::post('/create-customer',[ApiCustomerController::class, 'saveCustomer']);
Route::post('/update-customer',[ApiCustomerController::class, 'UpdateCustomer']);
Route::delete('/delete-customer',[ApiCustomerController::class, 'DeleteCustomer']);

Route::get('/Staff-list',[ApiStaffController::class, 'showStaff']);
Route::post('/create-staff',[ApiStaffController::class, 'saveStaff']);
Route::post('/update-staff',[ApiStaffController::class, 'updateStaff']);
Route::delete('/delete-staff',[ApiStaffController::class, 'deleteStaff']);

Route::get('/service-list',[ApiServiceController::class, 'showService']);
Route::post('/save-service',[ApiServiceController::class, 'saveService']);
Route::post('/update-service',[ApiServiceController::class, 'updateService']);
Route::delete('/delete-service',[ApiServiceController::class, 'deleteService']);
