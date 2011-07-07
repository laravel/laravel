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
		// Prepend a forward slash since all routes begin with one.
		$uri = ($uri != '/') ? '/'.$uri : $uri;

		if (is_null(static::$routes))
		{
			static::$routes = ( ! is_dir(APP_PATH.'routes')) ? require APP_PATH.'routes'.EXT : static::load($uri);
		}

		// Is there an exact match for the request?
		if (isset(static::$routes[$method.' '.$uri]))
		{
			return Request::$route = new Route($method.' '.$uri, static::$routes[$method.' '.$uri]);
		}

		foreach (static::$routes as $keys => $callback)
		{
			// Only check routes that have multiple URIs or wildcards. All other routes would
			// have been caught by a literal match.
			if (strpos($keys, '(') !== false or strpos($keys, ',') !== false )
			{
				foreach (explode(', ', $keys) as $key)
				{
					$key = str_replace(':num', '[0-9]+', str_replace(':any', '[a-zA-Z0-9\-_]+', $key));

					if (preg_match('#^'.$key.'$#', $method.' '.$uri))
					{
						// Remove the leading slashes from the route and request URIs. Also trim
						// the request method off of the route URI. This should get the request
						// and route URIs in the same format so we can extract the parameters.
						$uri = trim($uri, '/');
						$key = trim(substr($key, strlen($method.' ')), '/');

						return Request::$route = new Route($keys, $callback, static::parameters(explode('/', $uri), explode('/', $key)));
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
			$segments = explode('/', trim($uri, '/'));

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
	 * @param  array  $uri
	 * @param  array  $route
	 * @return array
	 */
	private static function parameters($uri, $route)
	{
		$parameters = array();

		for ($i = 0; $i < count($route); $i++)
		{
			if (strpos($route[$i], '(') === 0)
			{
				$parameters[] = $uri[$i];
			}
		}

		return $parameters;		
	}	

}