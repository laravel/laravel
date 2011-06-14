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
		// --------------------------------------------------------------
		// Split the request URI into segments.
		// --------------------------------------------------------------
		$uri_segments = explode('/', $uri);

		// --------------------------------------------------------------
		// Split the route URI into segments.
		// --------------------------------------------------------------
		$route_segments = explode('/', $route);

		// --------------------------------------------------------------
		// Initialize the array of parameters.
		// --------------------------------------------------------------
		$parameters = array();

		// --------------------------------------------------------------
		// Extract all of the parameters out of the URI.
		//
		// Any segment wrapped in parentheses is considered a parameter.
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