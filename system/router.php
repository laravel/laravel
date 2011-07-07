<?php namespace System;

class Router {

	/**
	 * All of the loaded routes.
	 *
	 * @var array
	 */
	public static $routes;

	/**
	 * Search a set of routes for the route matching a method and URI.
	 *
	 * @param  string  $method
	 * @param  string  $uri
	 * @return Route
	 */
	public static function route($method, $uri)
	{
		if (is_null(static::$routes))
		{
			static::$routes = (is_dir(APP_PATH.'routes')) ? static::load($uri) : require APP_PATH.'routes'.EXT;
		}

		// Put the request method and URI in route form. Routes begin with the request method and a forward slash.
		$uri = $method.' /'.trim($uri, '/');

		// Is there an exact match for the request?
		if (isset(static::$routes[$uri]))
		{
			return Request::$route = new Route($uri, static::$routes[$uri]);
		}

		foreach (static::$routes as $keys => $callback)
		{
			// Only check routes that have multiple URIs or wildcards. Other routes would be caught by a literal match.
			if (strpos($keys, '(') !== false or strpos($keys, ',') !== false )
			{
				foreach (explode(', ', $keys) as $key)
				{
					$key = str_replace(':num', '[0-9]+', str_replace(':any', '[a-zA-Z0-9\-_]+', $key));

					if (preg_match('#^'.$key.'$#', $uri))
					{
						return Request::$route = new Route($keys, $callback, static::parameters($uri, $key));
					}
				}				
			}
		}
	}

	/**
	 * Load the appropriate route file for the request URI.
	 *
	 * @param  string  $uri
	 * @return array
	 */
	public static function load($uri)
	{
		if ( ! file_exists(APP_PATH.'routes/home'.EXT))
		{
			throw new \Exception("A [home] route file is required when using a route directory.");					
		}

		if ($uri == '/')
		{
			return require APP_PATH.'routes/home'.EXT;
		}
		else
		{
			$segments = explode('/', $uri);

			if ( ! file_exists(APP_PATH.'routes/'.$segments[0].EXT))
			{
				return require APP_PATH.'routes/home'.EXT;
			}

			return array_merge(require APP_PATH.'routes/'.$segments[0].EXT, require APP_PATH.'routes/home'.EXT);
		}
	}

	/**
	 * Extract the parameters from a URI based on a route URI.
	 *
	 * Any route segment wrapped in parentheses is considered a parameter.
	 *
	 * @param  string  $uri
	 * @param  string  $route
	 * @return array
	 */
	private static function parameters($uri, $route)
	{
		return array_values(array_intersect_key(explode('/', $uri), preg_grep('/\(.+\)/', explode('/', $route))));	
	}	

}