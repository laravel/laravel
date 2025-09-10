<?php

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

// Default route redirect to login
Route::get('/', function () {
    return redirect('/login');
});

// Login routes
Route::get('/login', 'AuthController@showLoginForm');
Route::post('/login', 'AuthController@login');
Route::get('/logout', 'AuthController@logout');

// Protected routes - require login
Route::group(['middleware' => 'auth.custom'], function () {
    Route::get('/choosedate', function () {
        return view('choosedate');
    });
    
    Route::get('/home', function () {
        return view('home');
    });
});

Route::get('/welcome', function () {
    return view('welcome');
});
