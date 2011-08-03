<?php namespace System\Routing;

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
		return array_merge($this->load_nested_routes($uri), require $this->path.'routes'.EXT);
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
	 * @param  string  $path
	 * @return array
	 */
	public static function all($reload = false, $path = APP_PATH)
	{
		if ( ! is_null(static::$routes) and ! $reload) return static::$routes;

		$routes = require $path.'routes'.EXT;

		// Since route files can be nested deep within the route directory, we need to
		// recursively spin through the directory to find every file.
		$directoryIterator = new \RecursiveDirectoryIterator($path.'routes');

		$recursiveIterator = new \RecursiveIteratorIterator($directoryIterator, \RecursiveIteratorIterator::SELF_FIRST);

		foreach ($recursiveIterator as $file)
		{
			if (filetype($file) === 'file' and strpos($file, EXT) !== false)
			{
				$routes = array_merge(require $file, $routes);
			}
		}

		return static::$routes = $routes;
	}

}