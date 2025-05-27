<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::name('socialite.')->prefix('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\SocialiteController::class, 'index'])
        ->name('index');
    Route::get('{provider}/redirect', [App\Http\Controllers\SocialiteController::class, 'redirect'])
        ->name('redirect')
        ->middleware('guest');
    Route::get('{provider}/callback', [App\Http\Controllers\SocialiteController::class, 'callback'])
        ->name('callback')
        ->middleware('guest');
    Route::post('logout', [App\Http\Controllers\SocialiteController::class, 'logout'])
        ->name('logout')
        ->middleware('auth');
});
