<?php namespace Laravel\Routing;

use Laravel\Config;

class Loader {

	/**
	 * All of the routes for the application.
	 *
	 * @var array
	 */
	private static $routes;

	/**
	 * The path where the routes are located.
	 *
	 * @var string
	 */
	public $path;

	/**
	 * Create a new route loader instance.
	 *
	 * @param  string  $path
	 * @return void
	 */
	public function __construct($path)
	{
		$this->path = $path;
	}

	/**
	 * Load the appropriate routes for the request URI.
	 *
	 * @param  string
	 * @return array
	 */
	public function load($uri)
	{
		$base = (file_exists($path = $this->path.'routes'.EXT)) ? require $path : array();

		return array_merge($this->load_nested_routes(explode('/', $uri)), $base);
	}

	/**
	 * Load the appropriate routes from the routes directory.
	 *
	 * @param  array  $segments
	 * @return array
	 */
	private function load_nested_routes($segments)
	{
		// If the request URI only more than one segment, and the last segment contains a dot, we will
		// assume the request is for a specific format (users.json or users.xml) and strip off
		// everything after the dot so we can load the appropriate file.
		if (count($segments) > 0 and strpos(end($segments), '.') !== false)
		{
			$segment = array_pop($segments);

			array_push($segments, substr($segment, 0, strpos($segment, '.')));
		}

		// Work backwards through the URI segments until we find the deepest possible
		// matching route directory. Once we find it, we will return those routes.
		foreach (array_reverse($segments, true) as $key => $value)
		{
			if (file_exists($path = $this->path.'routes/'.implode('/', array_slice($segments, 0, $key + 1)).EXT))
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
	 * @param  bool    $reload
	 * @return array
	 */
	public static function all($path = APP_PATH, $reload = false)
	{
		if ( ! is_null(static::$routes) and ! $reload) return static::$routes;

		$routes = array();

		if (file_exists($path.'routes'.EXT))
		{
			$routes = array_merge($routes, require $path.'routes'.EXT);
		}

		if (is_dir($path.'routes'))
		{
			// Since route files can be nested deep within the route directory, we need to
			// recursively spin through the directory to find every file.
			$directoryIterator = new \RecursiveDirectoryIterator($path.'routes');

			$recursiveIterator = new \RecursiveIteratorIterator($directoryIterator, \RecursiveIteratorIterator::SELF_FIRST);

			foreach ($recursiveIterator as $file)
			{
				if (filetype($file) === 'file' and strpos($file, EXT) !== false)
				{
					$routes = array_merge($routes, require $file);
				}
			}
		}

		return static::$routes = $routes;
	}

}