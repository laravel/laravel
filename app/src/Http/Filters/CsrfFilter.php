<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Route;

class CsrfFilter {

	/**
	 * Run the request filter.
	 *
	 * @return mixed
	 */
	public function filter(Route $route, Request $request)
	{
		if (Session::token() != $request->input('_token'))
		{
			throw new Illuminate\Session\TokenMismatchException;
		}
	}

}