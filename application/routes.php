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
	| Simply tell Laravel the HTTP verbs and request URIs it should respond to.
	| You may respond to the GET, POST, PUT, or DELETE verbs. Enjoy the simplicity
	| and elegance of RESTful routing.
	|
	| Here is how to respond to a simple GET request to http://example.com/hello:
	|
	|		'GET /hello' => function()
	|		{
	|			return 'Hello World!';
	|		}
	|
	| You can even respond to more than one URI:
	|
	|		'GET /hello, GET /world' => function()
	|		{
	|			return 'Hello World!';
	|		}
	|
	| It's easy to allow URI wildcards using the (:num) or (:any) place-holders:
	|
	|		'GET /hello/(:any)' => function($name)
	|		{
	|			return "Welcome, $name.";
	|		}
	|
	*/

	'GET /' => function($request)
	{
		return View::make('home.index');
	},

);