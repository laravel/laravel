<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->group(function () {
    // Mostrar formulario de login
    Route::get('login', [AdminController::class, 'create'])->name('admin.login');

    // Procesar formulario de login
    Route::post('login', [AdminController::class, 'store'])->name('admin.login.request');
    Route::group(['middleware' => ['admin']], function () {
        // Ruta del dashboard protegida
        Route::resource('dashboard', AdminController::class)->only(['index']);
    });
});
