<?php namespace App\Http\Filters;

use Session;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Session\TokenMismatchException;

class CsrfFilter {

	/**
	 * Run the request filter.
	 *
	 * @param  \Illuminate\Routing\Route  $route
	 * @param  \Illuminate\Http\Request  $request
	 * @return void
	 * 
	 * @throws \Illuminate\Session\TokenMismatchException
	 */
	public function filter(Route $route, Request $request)
	{
		if ($request->ajax())
		{
			$token = $request->header('X-CSRF-Token');
		}
		else
		{
			$token = $request->input('_token');
		}
		
		if (Session::token() != $token)
		{
			throw new TokenMismatchException;
		}
	}

}
