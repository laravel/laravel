<?php namespace System\Routing;

use System\Config;

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

		// Merge all of the module paths in with the specified path so that all
		// active module routes will also be loaded. So, by default, this method
		// will search the application path and all active module paths for routes.
		$paths = array_merge(array($path), array_map(function($module) { return MODULE_PATH.$module.'/'; }, Config::get('application.modules')));

		$routes = array();

		foreach ($paths as $path)
		{
			if (file_exists($path.'routes'.EXT)) $routes = array_merge($routes, require $path.'routes'.EXT);

			if (is_dir($path.'routes'))
			{
				// Since route files can be nested deep within the route directory, we need to
				// recursively spin through the directory to find every file.
				$directoryIterator = new \RecursiveDirectoryIterator($path.'routes');

				$recursiveIterator = new \RecursiveIteratorIterator($directoryIterator, \RecursiveIteratorIterator::SELF_FIRST);

				foreach ($recursiveIterator as $file)
				{
					if (filetype($file) === 'file' and strpos($file, EXT) !== false) $routes = array_merge($routes, require $file);
				}
			}
		}

		return static::$routes = $routes;
	}

}