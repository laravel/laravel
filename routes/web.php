<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;

// use App\Http\Controllers\UserController;/
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

// Route::get('/', function () {
//     return view('layout');
// });
Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home');

// Role
// Route::resource('/role',RoleController::class);
Route::prefix('admin')->middleware('admin')->group(function(){
Route::get('/role','RoleController@index');
Route::get('/role-add','RoleController@create');
Route::post('/role-insert','RoleController@store');
Route::post('/role-update/{id}','RoleController@update');
Route::get('/role-edit/{id}','RoleController@edit');
Route::get('/role-delete/{id}','RoleController@destroy');

});

//Product
Route::resource('/product',ProductController::class);

//Users
Route::prefix('admin')->middleware('admin')->group(function(){
    Route::get('/users','UserController@index');
    Route::get('/users-add','UserController@create');
    Route::post('/users-insert','UserController@store');
    Route::post('/users-update/{id}','UserController@update');
    Route::get('/users-edit/{id}','UserController@edit');
    Route::get('/users-delete/{id}','UserController@destroy');
});


