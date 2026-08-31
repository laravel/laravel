<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// SPA fallback: let the Vue router handle any client-side route (e.g. /login, /dashboard)
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
