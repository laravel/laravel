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
	$data = array(
		'greeting' 		=> 	'Hello',
		'person' 		=>	'User',
		'statements'	=>	array(
			'a very simple framework', 
			'a test site for CS 165'
		),
		'test'			=>	true
	);
	return View::make('hello', $data);
});

