<?php namespace System\Routing;

class Loader {

	/**
	 * Load the appropriate routes for the request URI.
	 *
	 * @param  string
	 * @return array
	 */
	public function load($uri)
	{
		$base = require APP_PATH.'routes'.EXT;

		return (is_dir(APP_PATH.'routes') and $uri != '') ? array_merge($this->load_nested_routes($uri), $base) : $base;
	}

	/**
	 * Load the appropriate routes from the routes directory.
	 *
	 * This is done by working down the URI until we find the deepest
	 * possible matching route file.
	 *
	 * @param  string  $uri
	 * @return array
	 */
	private function load_nested_routes($uri)
	{
		$segments = explode('/', $uri);

		foreach (array_reverse($segments, true) as $key => $value)
		{
			if (file_exists($path = ROUTE_PATH.implode('/', array_slice($segments, 0, $key + 1)).EXT))
			{
				return require $path;
			}
		}

		return array();
	}

}