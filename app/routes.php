<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/




/* Ön kısım rotasyonları */

Route::get('/','Frontend_HomeController@home');
Route::get('makaledetay/{name}','Frontend_HomeController@detay');


Route::get('login','Backend_LoginController@login');
Route::get('admin','Backend_LoginController@login');
Route::post('adminpost','Backend_LoginController@loginpost');
Route::get('logout','Backend_LoginController@logout');


/* Admin kısmı rotasyonları */
Route::group(array('before' => 'auth'), function()
{
	
	Route::controller('ayarlar','Backend_AyarlarController');


});

