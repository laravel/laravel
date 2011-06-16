<?php namespace System;

class URL {

	/**
	 * Generate an application URL.
	 *
	 * @param  string  $url
	 * @return string
	 */
	public static function to($url = '', $https = false)
	{
		// ----------------------------------------------------
		// Return the URL unchanged if it is already formed.
		// ----------------------------------------------------
		if (strpos($url, '://') !== false)
		{
			return $url;
		}

		// ----------------------------------------------------
		// Get the base URL and index page.
		// ----------------------------------------------------
		$base = Config::get('application.url').'/'.Config::get('application.index').'/';

		// ----------------------------------------------------
		// Does the URL need an HTTPS protocol?
		// ----------------------------------------------------
		if (strpos($base, 'http://') === 0 and $https)
		{
			$base = 'https://'.substr($base, 7);
		}

		return $base.ltrim($url, '/');
	}

	/**
	 * Generate an application URL with HTTPS.
	 *
	 * @param  string  $url
	 * @return string
	 */
	public static function to_secure($url = '')
	{
		return static::to($url, true);
	}

	/**
	 * Generate a URL from a route name.
	 *
	 * @param  string  $name
	 * @param  array   $parameters
	 * @param  bool    $https
	 * @return string
	 */
	public static function to_route($name, $parameters = array(), $https = false)
	{
		if ( ! is_null($route = Route\Finder::find($name)))
		{
			// ----------------------------------------------------
			// Get the first URI assigned to the route.
			// ----------------------------------------------------
			$uris = explode(', ', key($route));

			$uri = substr($uris[0], strpos($uris[0], '/'));

			// ----------------------------------------------------
			// Replace any parameters in the URI.
			// ----------------------------------------------------
			foreach ($parameters as $parameter)
			{
				$uri = preg_replace('/\(\:any\)|\(\:num\)|\(.+\)/', $parameter, $uri, 1);
			}

			return static::to($uri, $https);
		}

		throw new \Exception("Error generating named route for route [$name]. Route is not defined.");
	}

	/**
	 * Generate a HTTPS URL from a route name.
	 *
	 * @param  string  $name
	 * @param  array   $parameters
	 * @return string
	 */
	public static function to_secure_route($name, $parameters = array())
	{
		return static::to_route($name, $parameters, true);
	}

	/**
	 * Generate a URL friendly "slug".
	 *
	 * @param  string  $title
	 * @param  string  $separator
	 * @return string
	 */
	public static function slug($title, $separator = '-')
	{
		// ----------------------------------------------------
		// Remove all characters that are not the separator,
		// letters, numbers, or whitespace.
		// ----------------------------------------------------
		$title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', Str::lower($title));

		// ----------------------------------------------------
		// Replace all separator characters and whitespace by
		// a single separator
		// ----------------------------------------------------
		$title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

		return trim($title, $separator);
	}

	/**
	 * Magic Method for dynamically creating route URLs.
	 */
	public static function __callStatic($method, $parameters)
	{
		// ----------------------------------------------------
		// Dynamically create a secure route URL.
		// ----------------------------------------------------
		if (strpos($method, 'to_secure_') === 0)
		{
			return static::to_route(substr($method, 10), $parameters, true);
		}

		// ----------------------------------------------------
		// Dynamically create a route URL.
		// ----------------------------------------------------
		if (strpos($method, 'to_') === 0)
		{
			return static::to_route(substr($method, 3), $parameters);
		}

		throw new \Exception("Method [$method] is not defined on the URL class.");
	}

}