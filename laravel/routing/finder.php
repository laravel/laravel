<?php namespace Laravel\Routing;

class Finder {

	/**
	 * The named routes that have been found so far.
	 *
	 * @var array
	 */
	public static $names = array();

	/**
	 * Find a named route in a given array of routes.
	 *
	 * @param  string  $name
	 * @param  array   $routes
	 * @return array
	 */
	public static function find($name, $routes)
	{
		if (array_key_exists($name, static::$names)) return static::$names[$name];

		$arrayIterator = new \RecursiveArrayIterator($routes);

		$recursiveIterator = new \RecursiveIteratorIterator($arrayIterator);

		// Since routes can be nested deep within sub-directories, we need to recursively
		// iterate through each directory and gather all of the routes.
		foreach ($recursiveIterator as $iterator)
		{
			$route = $recursiveIterator->getSubIterator();

			if (isset($route['name']) and $route['name'] == $name)
			{
				return static::$names[$name] = array($arrayIterator->key() => iterator_to_array($route));
			}
		}
	}

}