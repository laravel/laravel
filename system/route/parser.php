<?php namespace System\Route;

class Parser {

	/**
	 * Get the parameters that should be passed to the route callback.
	 *
	 * @param  string  $uri
	 * @param  string  $route
	 * @return array
	 */
	public static function parameters($uri, $route)
	{
		$parameters = array();

		$uri_segments = explode('/', $uri);
		$route_segments = explode('/', $route);

		// --------------------------------------------------------------
		// Any route segment wrapped in parentheses is a parameter.
		// --------------------------------------------------------------
		for ($i = 0; $i < count($route_segments); $i++)
		{
			if (strpos($route_segments[$i], '(') === 0)
			{
				$parameters[] = $uri_segments[$i];
			}
		}

		return $parameters;		
	}

}