<?php namespace App\Http\Controllers;

use App\Http\Requests\FileUploadRequest;
use Illuminate\Routing\Controller;

class HomeController extends Controller {

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
		return view('index');
	}


	/**
	 * @Post("/")
	 */
	public function store(FileUploadRequest $request)
	{
		return response()->json([
			'fileName' => $request->file('myFile')->getClientOriginalName(),
			'size' => $request->file('myFile')->getSize(),
			'error' => $request->file('myFile')->getError(),
		]);
	}
}
