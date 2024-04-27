<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\RoomtypeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\StaffDepartment;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\PageController;

use App\Http\Controllers\HomeController;
use App\Models\Room;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/',[HomeController::class,'home']);
Route::get('page/servicedetail',[PageController::class,'servicedetail']);
Route::get('/page/login',[PageController::class.'login']);
Route::get('page/about-us',[PageController::class,'about_us']);
Route::get('page/contact-us',[PageController::class,'contact_us']);
Route::get('page/booking',[PageController::class,'booking']);
Route::get('page/register',[PageController::class,'register']);
Route::get('page/frontlogin',[PageController::class,'frontlogin']);
Route::get('page/Room',[pageController::class,'room']);
Route::Post('page/Thankyou',[pageController::class,'Thankyou']);


    

// Route::post('admin/login',[AdminController::class,'check_login']);
// Route::get('admin/logout',[AdminController::class,'logout']);

// // Admin Dashboard;
// Route::get('admin/banner',[BannerController::class,'index']);
// Route::get('admin/banner/create',[BannerController::class,'create']);
// Route::post('admin/banner/store',[BannerController::class,'store']);
// Route::post('admin/banner/update/{id}',[BannerController::class,'update']);
// Route::get('admin/banner/delete/{id}',[BannerController::class,'delete']);

// Route::get('admin/roomtype',[RoomtypeController::class,'index']);
// Route::get('admin/roomtype/create',[RoomtypeController::class,'create']);
// Route::post('admin/roomtype/store',[RoomtypeController::class,'store']);
// Route::post('admin/roomtype/update/{id}',[RoomtypeController::class,'update']);
// Route::get('admin/roomtype/delete/{id}',[RoomtypeController::class,'delete']);

// Route::get('admin/room',[RoomController::class,'index']);
// Route::get('admin/room/create',[RoomController::class,'create']);
// Route::post('admin/room/store',[RoomController::class,'store']);
// Route::post('admin/room/update/{id}',[RoomController::class,'update']);
// Route::get('admin/room/delete/{id}',[RoomController::class,'delete']);

// Route::get('admin/customer',[CustomerController::class,'index']);
// Route::get('admin/customer/create',[CustomerController::class,'create']);
// Route::post('admin/customer/store',[CustomerController::class,'store']);
// Route::post('admin/customer/update/{id}',[CustomerController::class,'update']);
// Route::get('admin/customer/delete/{id}',[CustomerController::class,'delete']);

// Route::get('admin/staffdepartment',[StaffDepartment::class,'index']);
// Route::get('admin/staffdepartment/create',[StaffDepartment::class,'create']);
// Route::post('admin/staffdepartment/store',[StaffDepartment::class,'store']);
// Route::post('admin/staffdepartment/update/{id}',[StaffDepartment::class,'update']);
// Route::get('admin/staffdepartment/delete/{id}',[StaffDepartment::class,'delete']);

// Route::get('admin/staff',[StaffController::class,'index']);
// Route::get('admin/staff/create',[StaffController::class,'create']);
// Route::post('admin/staff/store',[StaffController::class,'store']);
// Route::post('admin/staff/update/{id}',[StaffController::class,'update']);
// Route::get('admin/staff/delete/{id}',[StaffController::class,'delete']);



// Route::get('admin/service',[ServiceController::class,'index']);
// Route::get('admin/service/create',[ServiceController::class,'create']);
// Route::post('admin/service/store',[ServiceController::class,'store']);
// Route::post('admin/service/update/{id}',[ServiceController::class,'update']);
// Route::get('admin/service/delete/{id}',[ServiceController::class,'delete']);

Route::get('admin/login',[AdminController::class,'login']);
Route::post('admin/login',[AdminController::class,'check_login']);
Route::get('admin/logout',[AdminController::class,'logout']);

// Admin Dashboard
Route::get('/dashboard',[AdminController::class,'dashboard']);

// Banner Routes
Route::get('admin/banner/{id}/delete',[BannerController::class,'destroy']);
Route::resource('admin/banner',BannerController::class);

// RoomType Routes
Route::get('admin/roomtype/{id}/delete',[RoomtypeController::class,'destroy']);
Route::resource('admin/roomtype',RoomtypeController::class);

// Room
Route::get('admin/rooms/{id}/delete',[RoomController::class,'destroy']);
Route::resource('admin/rooms',RoomController::class);

// Customer
Route::get('admin/customer/{id}/delete',[CustomerController::class,'destroy']);
Route::get('admin/customer/{id}/edit',[CustomerController::class,'edit']);
Route::resource('admin/customer',CustomerController::class);

// Delete Image
Route::get('admin/roomtypeimage/delete/{id}',[RoomtypeController::class,'destroy_image']);

// Department
Route::get('admin/department/{id}/delete',[StaffDepartment::class,'destroy']);
Route::resource('admin/department',StaffDepartment::class);

// Staff Payment
Route::get('admin/staff/payments/{id}',[StaffController::class,'all_payments']);
Route::get('admin/staff/payment/{id}/add',[StaffController::class,'add_payment']);
Route::post('admin/staff/payment/{id}',[StaffController::class,'save_payment']);
Route::get('admin/staff/payment/{id}/{staff_id}/delete',[StaffController::class,'delete_payment']);
// Staff CRUD
Route::get('admin/staff/{id}/delete',[StaffController::class,'destroy']);
Route::resource('admin/staff',StaffController::class);


// Booking

Route::get('admin/booking',[BookingController::class,'index']);
Route::get('admin/booking/create',[BookingController::class,'create']);
Route::post('admin/booking/store',[BookingController::class,'store']);
Route::post('admin/booking/update/{id}',[BookingController::class,'update']);
Route::get('admin/booking/delete/{id}',[BookingController::class,'delete']);

Route::get('login',[CustomerController::class,'login']);
Route::post('customer/login',[CustomerController::class,'customer_login']);
Route::get('register',[CustomerController::class,'register']);
Route::get('logout',[CustomerController::class,'logout']);

Route::get('booking',[BookingController::class,'front_booking']);
Route::get('booking/success',[BookingController::class,'booking_payment_success']);
Route::get('booking/fail',[BookingController::class,'booking_payment_fail']);




// Service CRUD
Route::get('admin/service/{id}/delete',[ServiceController::class,'destroy']);
Route::resource('admin/service',ServiceController::class);

// Testimonial
Route::get('customer/add-testimonial',[HomeController::class,'add_testimonial']);
Route::post('customer/save-testimonial',[HomeController::class,'save_testimonial']);
Route::get('admin/testimonial/{id}/delete',[AdminController::class,'destroy_testimonial']);
Route::get('admin/testimonials',[AdminController::class,'testimonials']);



