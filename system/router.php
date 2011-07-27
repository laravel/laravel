<?php namespace System;

class Router {

	/**
	 * All of the loaded routes keyed by route file.
	 *
	 * @var array
	 */
	public static $routes = array();

	/**
	 * Simulate a request to a given route. Useful for implementing HMVC.
	 *
	 * @param  array|string  $parameters
	 * @return Response
	 */
	public static function call($parameters)
	{
		$route = static::route('GET', (is_array($parameters)) ? implode('/', $parameters) : (string) $parameters);

		if ( ! is_null($route))
		{
			return $route->call();
		}
	}

	/**
	 * Search a set of routes for the route matching a method and URI.
	 *
	 * @param  string  $method
	 * @param  string  $uri
	 * @return Route
	 */
	public static function route($method, $uri)
	{
		$routes = static::load($uri);

		// Put the request method and URI in route form. 
		// Routes begin with the request method and a forward slash.
		$uri = $method.' /'.trim($uri, '/');

		// Is there an exact match for the request?
		if (isset($routes[$uri]))
		{
			return Request::$route = new Route($uri, $routes[$uri]);
		}

		foreach ($routes as $keys => $callback)
		{
			// Only check routes that have multiple URIs or wildcards.
			// Other routes would have been caught by the check for literal matches.
			if (strpos($keys, '(') !== false or strpos($keys, ',') !== false )
			{
				foreach (explode(', ', $keys) as $key)
				{
					if (preg_match('#^'.static::translate_wildcards($key).'$#', $uri))
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
		$base = (isset(static::$routes[$path = APP_PATH.'routes'.EXT])) ? static::$routes[$path] : static::$routes[$path] = require $path;

		return (is_dir(APP_PATH.'routes') and $uri !== '') ? array_merge(static::load_from_directory($uri), $base) : $base;
	}

	/**
	 * Load the appropriate route file from the routes directory.
	 *
	 * @param  string  $uri
	 * @return array
	 */
	private static function load_from_directory($uri)
	{
		$segments = explode('/', $uri);

		// Route files can be nested deep within sub-directories. 
		// Iterate backwards through the URI looking for the deepest matching file.
		foreach (array_reverse($segments, true) as $key => $value)
		{
			if (isset(static::$routes[$path = ROUTE_PATH.implode('/', array_slice($segments, 0, $key + 1)).EXT]))
			{
				return static::$routes[$path];
			}
			elseif (file_exists($path))
			{
				return static::$routes[$path] = require $path;
			}
		}

		return array();
	}

	/**
	 * Translate route URI wildcards into actual regular expressions.
	 *
	 * @param  string  $key
	 * @return string
	 */
	private static function translate_wildcards($key)
	{
		$replacements = 0;

		// For optional parameters, first translate the wildcards to their regex equivalent, sans the ")?" ending.
		$key = str_replace(array('/(:num?)', '/(:any?)'), array('(?:/([0-9]+)', '(?:/([a-zA-Z0-9\-_]+)'), $key, $replacements);
		
		// Now, to properly close the regular expression, we need to append a ")?" for each optional segment in the route.
		if ($replacements > 0)
		{
			$key .= str_repeat(')?', $replacements);
		}

		return str_replace(array(':num', ':any'), array('[0-9]+', '[a-zA-Z0-9\-_]+'), $key);
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