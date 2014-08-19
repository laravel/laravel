<?php

use Illuminate\Http\Request;

class AuthFilter {

	/**
	 * Run the request filter.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return mixed
	 */
	public function filter(Request $request)
	{
		if (Auth::guest())
		{
			if ($request->ajax())
			{
				return Response::make('Unauthorized', 401);
			}
			else
			{
				return Redirect::guest('login');
			}
		}
	}

}