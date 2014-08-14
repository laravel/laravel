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


Route::get('/', function()
{
	return 'Ön Tasarım';
});


/* Public Rotasyonlar */
Route::get('login','Backend_LoginController@login');
Route::get('logout','Backend_LoginController@logout');
Route::post('loginpost','Backend_LoginController@loginpost')->before('csrf');


/* Admin kısmı rotasyonları */
Route::group(array('before' => 'auth'), function()
{

	Route::controller('panel','Backend_PanelController');

});

