<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Application Routes
	|--------------------------------------------------------------------------
	|
	| Here is the public API of your application. To add functionality to your
	| application, you just add to the array located in this file.
	|
	| It's a breeze. Simply tell Laravel the request URIs it should respond to.
	|
	| Alternatively, you can organize your routes in a /routes/ subfolder.
	| See http://laravel.com/docs/start/routes#organize for an example.
	|
	*/

	'GET /' => function()
	{
		return View::make('home/index');
	},

);