<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Application Routes
	|--------------------------------------------------------------------------
	|
	| Here is the "definition", or the public API, of your application.
	|
	| To add functionality to your application, you add to the array located
	| in this file. It's a breeze. Just tell Laravel the request method and
	| URI a function should respond to.
	|
	| To learn more, check out: http://laravel.com/docs/basics/routes
	|
	*/

	'GET /' => function()
	{
		return View::make('home/index');
	},

);