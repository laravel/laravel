<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;

Route::get('/', fn() => view('welcome'));

Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn() => redirect(Auth::user()->role == 'admin' ? '/admin/users' : '/perfil'));
    Route::get('/admin/users', [UserController::class, 'index'])->middleware('admin')->name('admin.users');
    Route::get('/perfil', [UserController::class, 'perfil'])->name('perfil');
});

