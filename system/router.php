<?php namespace System;

class Router {

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
	 * Search a set of routes for the route matching a method and URI.
	 *
	 * @param  string  $method
	 * @param  string  $uri
	 * @return Route
	 */
	public static function route($method, $uri)
	{
		// --------------------------------------------------------------
		// Add a forward slash to the URI if necessary.
		// --------------------------------------------------------------
		$uri = ($uri != '/') ? '/'.$uri : $uri;

		// --------------------------------------------------------------
		// Load all of the application routes.
		// --------------------------------------------------------------
		static::$routes = require APP_PATH.'routes'.EXT;

		// --------------------------------------------------------------
		// Is there an exact match for the request?
		// --------------------------------------------------------------
		if (isset(static::$routes[$method.' '.$uri]))
		{
			return new Route(static::$routes[$method.' '.$uri]);
		}

		// --------------------------------------------------------------
		// No exact match... check each route individually.
		// --------------------------------------------------------------
		foreach (static::$routes as $keys => $callback)
		{
			// --------------------------------------------------------------
			// Only check routes that have multiple URIs or wildcards.
			// All other routes would have been caught by a literal match.
			// --------------------------------------------------------------
			if (strpos($keys, '(') !== false or strpos($keys, ',') !== false )
			{
				// --------------------------------------------------------------
				// Multiple routes can be assigned to a callback using commas.
				// --------------------------------------------------------------
				foreach (explode(', ', $keys) as $route)
				{
					// --------------------------------------------------------------
					// Change wildcards into regular expressions.
					// --------------------------------------------------------------
					$route = str_replace(':num', '[0-9]+', str_replace(':any', '.+', $route));

					// --------------------------------------------------------------
					// Test the route for a match.
					// --------------------------------------------------------------
					if (preg_match('#^'.$route.'$#', $method.' '.$uri))
					{
						return new Route($callback, static::parameters(explode('/', $uri), explode('/', $route)));
					}
				}				
			}
		}
	}

	/**
	 * Find a route by name.
	 *
	 * @param  string  $name
	 * @return array
	 */
	public static function find($name)
	{
		// ----------------------------------------------------
		// Have we already looked up this named route?
		// ----------------------------------------------------
		if (array_key_exists($name, static::$names))
		{
			return static::$names[$name];
		}

		// ----------------------------------------------------
		// Instantiate the recursive array iterator.
		// ----------------------------------------------------
		$arrayIterator = new \RecursiveArrayIterator(static::$routes);

		// ----------------------------------------------------
		// Instantiate the recursive iterator iterator.
		// ----------------------------------------------------
		$recursiveIterator = new \RecursiveIteratorIterator($arrayIterator);

		// ----------------------------------------------------
		// Iterate through the routes searching for a route
		// name that matches the given name.
		// ----------------------------------------------------
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
	 * Get the parameters that should be passed to the route callback.
	 *
	 * @param  array  $uri_segments
	 * @param  array  $route_segments
	 * @return array
	 */
	private static function parameters($uri_segments, $route_segments)
	{
		$parameters = array();

		// --------------------------------------------------------------
		// Spin through the route segments looking for parameters.
		// --------------------------------------------------------------
		for ($i = 0; $i < count($route_segments); $i++)
		{
			// --------------------------------------------------------------
			// Any segment wrapped in parentheses is a parameter.
			// --------------------------------------------------------------
			if (strpos($route_segments[$i], '(') === 0)
			{
				$parameters[] = $uri_segments[$i];
			}
		}

		return $parameters;		
	}

}