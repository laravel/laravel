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

// Test route for debugging
Route::get('/test-login', function() {
    return view('test-login');
});

// Debug route
Route::get('/debug', function() {
    return view('debug');
});

// Test controller
Route::get('/test-controller', function() {
    try {
        $controller = new \App\Http\Controllers\AuthController();
        return 'AuthController loaded successfully';
    } catch (Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

// Protected routes - require login
Route::group(['middleware' => 'auth.custom'], function () {
    Route::get('/choosedate', function () {
        return view('choosedate_fixed');
    });
    
    Route::get('/home', function () {
        return view('home');
    });
    
    // Employee Management Routes
    Route::get('/employe', 'EmployeeController@index')->name('employee.index');
    Route::post('/employee', 'EmployeeController@store')->name('employee.store');
    Route::get('/employee/{id}/edit', 'EmployeeController@edit')->name('employee.edit');
    Route::put('/employee/{id}', 'EmployeeController@update')->name('employee.update');
    Route::delete('/employee/{id}', 'EmployeeController@destroy')->name('employee.destroy');
    
    // Employee List Route
    Route::get('/employees', function () {
        return view('employees');
    })->name('employees.index');
    
    // Additional Employee Routes
    Route::get('/employee/statistics', 'EmployeeController@getStatistics')->name('employee.statistics');
    Route::get('/employee/export', 'EmployeeController@export')->name('employee.export');
    Route::get('/employee/search', 'EmployeeController@search')->name('employee.search');
    
    // Payroll Routes (placeholder for future development)
    Route::get('/payroll', function () {
        return view('payroll.index');
    })->name('payroll.index');
    
    // Reports Routes (placeholder for future development)
    Route::get('/reports', function () {
        return view('reports.index');
    })->name('reports.index');
});

Route::get('/welcome', function () {
    return view('welcome');
});
