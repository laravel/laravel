<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;

// Ruta para mostrar la vista de autenticación (login/registro)
Route::get('/', function () {
    return view('auth');
})->name('auth');

// Ruta para procesar el registro
Route::post('/register', [RegisterController::class, 'store'])->name('register');

// Ruta para procesar el inicio de sesión
Route::post('/login', [LoginController::class, 'store'])->name('login');

// Ruta para cerrar sesión
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// Ruta protegida que solo pueden ver usuarios autenticados
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware('auth')->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
