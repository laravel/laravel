<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Application Routes
	|--------------------------------------------------------------------------
	|
	| Here is the public API of your application. To add functionality to your
	| application, you add to the array located in this file.
	|
	| It's a breeze. Just tell Laravel the request URIs it should respond to.
	|
	*/

	'GET /' => function()
	{
		return View::make('home/index');
	},

);