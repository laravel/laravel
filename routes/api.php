<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\ApiRoomController;
use App\Http\Controllers\Api\ApiCustomerController;
use App\Http\Controllers\Api\Apiroomtype;
use App\Http\Controllers\Api\ApiStaffController;
use App\Http\Controllers\Api\ApiServiceController;
use App\Http\Controllers\Api\ApiBookingController;



Route::get('/banner-list',[ApiController::class, 'showBanner']);
Route::Post('/save-banner',[ApiController::class, 'saveBanner']);
Route::Post('/update-banner',[ApiController::class, 'updateBanner']);
Route::Post('/delete-banner',[ApiController::class, 'deleteBanner']);

Route::get('/room-list',[ApiRoomController::class, 'showRoom']);
Route::post('/save-room',[ApiRoomController::class, 'saveRoom']);
Route::post('/update-room',[ApiRoomController::class, 'updateRoom']);
Route::post('/delete-room',[ApiRoomController::class, 'deleteRoom']);


Route::get('/customer-list',[ApiCustomerController::class, 'showCustomer']);
Route::post('/create-customer',[ApiCustomerController::class, 'saveCustomer']);
Route::post('/update-customer',[ApiCustomerController::class, 'UpdateCustomer']);
Route::Post('/delete-customer',[ApiCustomerController::class, 'deleteCustomer']);

Route::get('/Staff-list',[ApiStaffController::class, 'showStaff']);
Route::post('/create-staff',[ApiStaffController::class, 'saveStaff']);
Route::post('/update-staff',[ApiStaffController::class, 'updateStaff']);
Route::post('/delete-staff',[ApiStaffController::class, 'deleteStaff']);

Route::get('/service-list',[ApiServiceController::class, 'showService']);
Route::post('/save-service',[ApiServiceController::class, 'saveService']);
Route::post('/update-service',[ApiServiceController::class, 'updateService']);
Route::post('/delete-service',[ApiServiceController::class, 'deleteService']);

Route::get('/roomtype-list',[Apiroomtype::class, 'showRoomtype']);
Route::post('/save-roomtype',[Apiroomtype::class, 'saveRoomtype']);
Route::post('/update-roomtype',[ApiRoomtype::class, 'updateRoomtype']);
Route::post('/delete-roomtype',[ApiRoomtype::class, 'deleteRoomtype']);

Route::get('/booking-list',[ApiBookingController::class, 'showBooking']);
Route::post('/save-booking',[ApiBookingController::class, 'saveBooking']);
Route::post('/update-booking',[ApiBookingController::class, 'updateBooking']);
Route::post('/delete-booking',[ApiBookingController::class, 'deleteBooking']);
