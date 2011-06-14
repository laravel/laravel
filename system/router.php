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
		// Force the URI to have a forward slash.
		// --------------------------------------------------------------
		$uri = ($uri != '/') ? '/'.$uri : $uri;

		// --------------------------------------------------------------
		// If a route directory is being used, load the route file
		// corresponding to the first segment of the URI.
		// --------------------------------------------------------------
		if (is_dir(APP_PATH.'routes'))
		{
			if ($uri == '/')
			{
				if ( ! file_exists(APP_PATH.'routes/home'.EXT))
				{
					throw new \Exception("A [home] route file is required when using a route directory.");					
				}

				static::$routes = require APP_PATH.'routes/home'.EXT;
			}
			else
			{
				$segments = explode('/', trim($uri, '/'));

				if ( ! file_exists(APP_PATH.'routes/'.$segments[0].EXT))
				{
					throw new \Exception("No route file defined for routes beginning with [".$segments[0]."]");
				}

				static::$routes = require APP_PATH.'routes/'.$segments[0].EXT;
			}
		}
		// --------------------------------------------------------------
		// If no route directory is being used, we can simply load the
		// routes file from the application directory.
		// --------------------------------------------------------------
		else
		{
			static::$routes = require APP_PATH.'routes'.EXT;
		}

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
				foreach (explode(', ', $keys) as $route)
				{
					$route = str_replace(':num', '[0-9]+', str_replace(':any', '.+', $route));

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
		if (array_key_exists($name, static::$names))
		{
			return static::$names[$name];
		}

		$arrayIterator = new \RecursiveArrayIterator(static::$routes);
		$recursiveIterator = new \RecursiveIteratorIterator($arrayIterator);

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