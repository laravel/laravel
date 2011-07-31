<?php namespace System\Routing;

class Loader {

	/**
	 * Load the appropriate routes for the request URI.
	 *
	 * @param  string
	 * @return array
	 */
	public static function load($uri)
	{
		$base = require APP_PATH.'routes'.EXT;

		if ( ! is_dir(APP_PATH.'routes') or $uri == '')
		{
			return $base;
		}

		list($routes, $segments) = array(array(), explode('/', $uri));

		foreach (array_reverse($segments, true) as $key => $value)
		{
			if (file_exists($path = ROUTE_PATH.implode('/', array_slice($segments, 0, $key + 1)).EXT))
			{
				$routes = require $path;
			}
		}

		return array_merge($routes, $base);
	}	

}