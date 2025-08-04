<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->group(function () {
    // Show login form
    Route::get('login', [AdminController::class, 'create'])->name('admin.login');

    Route::group(['middleware' => ['admin']], function () {
        // Dashboard route
        Route::resource('dashboard', AdminController::class)->only(['index']);
    });
});
