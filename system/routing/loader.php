<?php namespace System\Routing;

class Loader {

	/**
	 * All of the routes for the application.
	 *
	 * @var array
	 */
	private static $routes;

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
	 * @param  string  $uri
	 * @return array
	 */
	private function load_nested_routes($uri)
	{
		$segments = explode('/', $uri);

		// Work backwards through the URI segments until we find the deepest possible
		// matching route file in the routes directory.
		foreach (array_reverse($segments, true) as $key => $value)
		{
			if (file_exists($path = ROUTE_PATH.implode('/', array_slice($segments, 0, $key + 1)).EXT))
			{
				return require $path;
			}
		}

		return array();
	}

	/**
	 * Get all of the routes for the application.
	 *
	 * To improve performance, this operation will only be performed once. The routes
	 * will be cached and returned on every subsequent call.
	 *
	 * @return array
	 */
	public static function everything()
	{
		if ( ! is_null(static::$routes)) return static::$routes;

		$routes = require APP_PATH.'routes'.EXT;

		if (is_dir(APP_PATH.'routes'))
		{
			// Since route files can be nested deep within the route directory, we need to
			// recursively spin through the directory to find every file.
			$directoryIterator = new \RecursiveDirectoryIterator(APP_PATH.'routes');

			$recursiveIterator = new \RecursiveIteratorIterator($directoryIterator, \RecursiveIteratorIterator::SELF_FIRST);

			foreach ($recursiveIterator as $file)
			{
				if (filetype($file) === 'file' and strpos($file, EXT) !== false)
				{
					$routes = array_merge(require $file, $routes);
				}
			}
		}

		return static::$routes = $routes;
	}

}