<?php namespace System;

class URL {

	/**
	 * Generate an application URL.
	 *
	 * If the given URL is already well-formed, it will be returned unchanged.
	 *
	 * @param  string  $url
	 * @param  bool    $https
	 * @param  bool    $asset
	 * @return string
	 */
	public static function to($url = '', $https = false, $asset = false)
	{
		if (filter_var($url, FILTER_VALIDATE_URL) !== false)
		{
			return $url;
		}

		$base = Config::get('application.url').'/'.Config::get('application.index');

		if ($asset and Config::get('application.index') !== '')
		{
			$base = str_replace('/'.Config::get('application.index'), '', $base);
		}

		if ($https and strpos($base, 'http://') === 0)
		{
			$base = 'https://'.substr($base, 7);
		}

		return rtrim($base, '/').'/'.ltrim($url, '/');
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
	 * Generate an application URL to an asset. The index file
	 * will not be added to the URL.
	 *
	 * @param  string  $url
	 * @return string
	 */
	public static function to_asset($url)
	{
		return static::to($url, Request::is_secure(), true);
	}

	/**
	 * Generate a URL from a route name.
	 *
	 * For routes that have wildcard parameters, an array may be passed as the
	 * second parameter to the method. The values of this array will be used
	 * to fill the wildcard segments of the route URI.
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
			$uris = explode(', ', key($route));

			$uri = substr($uris[0], strpos($uris[0], '/'));

			foreach ($parameters as $parameter)
			{
				$uri = preg_replace('/\(.+?\)/', $parameter, $uri, 1);
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
		$title = html_entity_decode(Str::ascii($title), ENT_QUOTES, Config::get('application.encoding'));

		// Remove all characters that are not the separator, letters, numbers, or whitespace.
		$title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', Str::lower($title));

		// Replace all separator characters and whitespace by a single separator
		$title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

		return trim($title, $separator);
	}

	/**
	 * Magic Method for dynamically creating route URLs.
	 */
	public static function __callStatic($method, $parameters)
	{
		$parameters = (isset($parameters[0])) ? $parameters[0] : array();

		// Dynamically create a secure route URL.
		if (strpos($method, 'to_secure_') === 0)
		{
			return static::to_route(substr($method, 10), $parameters, true);
		}

		// Dynamically create a route URL.
		if (strpos($method, 'to_') === 0)
		{
			return static::to_route(substr($method, 3), $parameters);
		}

		throw new \Exception("Method [$method] is not defined on the URL class.");
	}

}