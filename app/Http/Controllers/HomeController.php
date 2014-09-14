<?php namespace App\Http\Controllers;

use App\Http\Requests\FileInputRequest;
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
	|	Route::get('/', 'HomeController@index');
	|
	*/

	public function index()
	{
		return view('hello');
	}

    public function postWith(FileInputRequest $request)
    {
        print('Post with request object:');

        dd($request->all());
    }

    public function postWithout()
    {
        print('Post without the request object:');

        dd(\Input::all());
    }
}
