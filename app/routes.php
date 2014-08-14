<?php

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

