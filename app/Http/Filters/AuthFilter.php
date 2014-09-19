<?php namespace App\Http\Filters;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Auth, Redirect, Response;

class AuthFilter {

	/**
	 * Run the request filter.
	 *
	 * @param  \Illuminate\Routing\Route  $route
	 * @param  \Illuminate\Http\Request  $request
	 * @return mixed
	 */
	public function filter(Route $route, Request $request)
	{
		if (Auth::guest())
		{
			if ($request->ajax())
			{
				return Response::make('Unauthorized', 401);
			}
			else
			{
				return Redirect::guest('auth/login');
			}
		}
	}

}
