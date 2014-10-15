<?php namespace App\Http\Controllers;

class HomeController {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| Controller methods are called when a request enters the application
	| with their assigned URI. The URI a method responds to may be set
	| via simple annotations. Here is an example to get you started!
	|
	*/

	/**
	 * @Get("/")
	 */
	public function index()
	{
		return view('hello');
	}

}
