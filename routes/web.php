<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ConsultController;
use App\Http\Controllers\TrackingController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/consult', [ConsultController::class, 'index'])->name('consult');
Route::match(['get', 'post'], '/tracking', [TrackingController::class, 'index'])->name('tracking');
