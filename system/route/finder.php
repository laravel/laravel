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
		if (is_null(static::$routes))
		{
			static::$routes = (is_dir(APP_PATH.'routes')) ? static::load() : require APP_PATH.'routes'.EXT;
		}

		if (array_key_exists($name, static::$names))
		{
			return static::$names[$name];
		}

		// We haven't located the route before, so we'll need to iterate through each
		// route to find the matching name.
		$arrayIterator = new \RecursiveArrayIterator(static::$routes);
		$recursiveIterator = new \RecursiveIteratorIterator($arrayIterator);

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