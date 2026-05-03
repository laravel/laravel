<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MenuController;

Route::get('/', [MenuController::class, 'index'])->name('home');
