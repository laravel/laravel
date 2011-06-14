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
		// --------------------------------------------------------------
		// Load the routes if we haven't already.
		// --------------------------------------------------------------
		if (is_null(static::$routes))
		{
			static::$routes = (is_dir(APP_PATH.'routes')) ? static::load() : require APP_PATH.'routes'.EXT;
		}

		// --------------------------------------------------------------
		// Have we already located this route by name?
		// --------------------------------------------------------------
		if (array_key_exists($name, static::$names))
		{
			return static::$names[$name];
		}

		// --------------------------------------------------------------
		// Instantiate the SPL array iterators.
		// --------------------------------------------------------------
		$arrayIterator = new \RecursiveArrayIterator(static::$routes);
		$recursiveIterator = new \RecursiveIteratorIterator($arrayIterator);

		// --------------------------------------------------------------
		// Iterate over the routes and find the named route.
		// --------------------------------------------------------------
		foreach ($recursiveIterator as $iterator)
		{
			$route = $recursiveIterator->getSubIterator();

			if ($route['name'] == $name)
			{
				return static::$names[$name] = array($arrayIterator->key() => iterator_to_array($route));
			}
		}
	}

	/**
	 * Load all of the routes from the routes directory.
	 *
	 * @return array
	 */
	private static function load()
	{
		$routes = array();

		// --------------------------------------------------------------
		// Merge all of the various route files together.
		// --------------------------------------------------------------
		foreach (glob(APP_PATH.'routes/*') as $file)
		{
			if (filetype($file) == 'file')
			{
				$routes = array_merge(require $file, $routes);
			}			
		}

		return $routes;
	}

}