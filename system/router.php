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
		// --------------------------------------------------------------
		// Prepend a forward slash since all routes begin with one.
		// --------------------------------------------------------------
		$uri = ($uri != '/') ? '/'.$uri : $uri;

		if (is_null(static::$routes))
		{
			static::$routes = Route\Loader::load($uri);
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
				// --------------------------------------------------------------
				// Routes can be comma-delimited, so spin through each one.
				// --------------------------------------------------------------
				foreach (explode(', ', $keys) as $route)
				{
					$route = str_replace(':num', '[0-9]+', str_replace(':any', '[a-zA-Z0-9\-_]+', $route));

					if (preg_match('#^'.$route.'$#', $method.' '.$uri))
					{
						return new Route($callback, Route\Parser::parameters($uri, $route));
					}
				}				
			}
		}
	}

}