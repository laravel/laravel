<?php namespace System\Route;

class Finder {

	/**
	 * All of the loaded routes.
	 *
	 * @var array
	 */
	public static $routes;

	/**
	 * The named routes that have been found so far.
	 *
	 * @var array
	 */
	public static $names = array();

	/**
	 * Find a route by name.
	 *
	 * @param  string  $name
	 * @return array
	 */
	public static function find($name)
	{
		// This class maintains its own list of routes because the router only loads routes that
		// are applicable to the current request URI. But, this class obviously needs access
		// to all of the routes, not just the ones applicable to the request URI.
		if (is_null(static::$routes))
		{
			static::$routes = require APP_PATH.'routes'.EXT;

			if (is_dir(APP_PATH.'routes'))
			{
				static::$routes = array_merge(static::load(), static::$routes);
			}
		}

		if (array_key_exists($name, static::$names))
		{
			return static::$names[$name];
		}

		$recursiveIterator = new \RecursiveIteratorIterator($arrayIterator = new \RecursiveArrayIterator(static::$routes));

		foreach ($recursiveIterator as $iterator)
		{
			$route = $recursiveIterator->getSubIterator();

			if (isset($route['name']) and $route['name'] == $name)
			{
				return static::$names[$name] = array($arrayIterator->key() => iterator_to_array($route));
			}
		}
	}

	/**
	 * Load all of the routes from the routes directory.
	 *
	 * All of the various route files will be merged together
	 * into a single array that can be searched.
	 *
	 * @return array
	 */
	private static function load()
	{
		$routes = array();

		$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(APP_PATH.'routes'), \RecursiveIteratorIterator::SELF_FIRST);

		foreach ($iterator as $file)
		{
			if (filetype($file) === 'file')
			{
				$routes = array_merge(require $file, $routes);
			}
		}

		return $routes;
	}

}