<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/ciber', function () {
    return view('ciber');
});

Route::get('/dd', function () {
    dd('dd');
});


