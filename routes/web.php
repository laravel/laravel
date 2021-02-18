<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::view('/','pages.home');
Route::view('/about','pages.about');
Route::view('/politica-de-privacidad','pages.privacy');
Route::view('/politica-de-cookies','pages.cookies')->name('cookies');
Route::view('{path}', 'pages.wizard')->where('path', 'contact|email-sent');
