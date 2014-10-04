<?php namespace App\Http\Controllers;

use Illuminate\Routing\Controller;

class HomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	$router->get('/', 'HomeController@index');
	|
	*/

	/**
	 * @Get("/", as="home")
	 */
	public function index()
	{
		return view('hello');
	}

}
