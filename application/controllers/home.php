<?php

class Home_Controller extends Controller {

	/*
	|--------------------------------------------------------------------------
	| The Default Controller
	|--------------------------------------------------------------------------
	|
	| Instead of using RESTful routes and anonymous functions, you may wish to
	| use controllers to organize your application API. You'll love them.
	|
	| To start using this controller, simple remove the default route from the
	| application "routes.php" file. Laravel is smart enough to find this
	| controller and call the default method, which is "index".
	|
	| This controller responds to URIs beginning with "home", and it also
	| serves as the default controller for the application, meaning it
	| handles requests to the root of the application.
	|
	| You can respond to requests to "/home/profile" like so:
	|
	|		public function profile()
	|		{
	|			return "This is your profile!";
	|		}
	|
	| Any extra segments are passed to the method as parameters:
	|
	|		public function profile($id)
	|		{
	|			return "This is the profile for user {$id}.";
	|		}
	|
	*/

	public function index()
	{
		return View::make('home.index');
	}

}