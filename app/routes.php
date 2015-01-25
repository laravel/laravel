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

Route::get('/login', function()
{
	Auth::loginUsingId(1);
	sleep(5);

	echo "Logged in: " .  (Auth::id() ? 'Yes' : 'No');
});

Route::get('/ping', function()
{
	sleep(5);

	echo "Logged in: " .  (Auth::id() ? 'Yes' : 'No');
});







Route::get('/', function()
{
	echo "Logged in: " .  (Auth::id() ? 'Yes' : 'No');
});

Route::get('/logout', function()
{
	Auth::logout();

	echo "Logged in: " .  (Auth::id() ? 'Yes' : 'No');
});
