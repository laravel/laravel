<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
})->name('login');
Route::get('/register', function () {
})->name('register');
