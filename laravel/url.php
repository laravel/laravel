<?php namespace Laravel;

class URL {

	/**
	 * Generate an application URL.
	 *
	 * If the given URL is already well-formed, it will be returned unchanged.
	 *
	 * <code>
	 *		// Generate an application URL from a given URI
	 *		echo URL::to('user/profile');
	 *
	 *		// Generate an application URL with HTTPS
	 *		echo URL::to('user/profile', true);
	 * </code>
	 *
	 * @param  string  $url
	 * @param  bool    $https
	 * @return string
	 */
	public static function to($url = '', $https = false)
	{
		if (filter_var($url, FILTER_VALIDATE_URL) !== false) return $url;

		$base = Config::get('application.url').'/'.Config::get('application.index');

		if ($https) $base = preg_replace('~http://~', 'https://', $base, 1);

		return rtrim($base, '/').'/'.ltrim($url, '/');
	}

	/**
	 * Generate an application URL with HTTPS.
	 *
	 * <code>
	 *		// Generate an application URL with HTTPS
	 *		echo URL::to_secure('user/profile');
	 * </code>
	 *
	 * @param  string  $url
	 * @return string
	 */
	public static function to_secure($url = '')
	{
		return static::to($url, true);
	}

	/**
	 * Generate an application URL to an asset.
	 *
	 * The index file will not be added to asset URLs. If the HTTPS option is not
	 * specified, HTTPS will be used when the active request is also using HTTPS.
	 *
	 * <code>
	 *		// Generate a URL to an asset
	 *		echo URL::to_asset('img/picture.jpg');
	 *
	 *		// Generate a URL to a an asset with HTTPS
	 *		echo URL::to_asset('img/picture.jpg', true);
	 * </code>
	 *
	 * @param  string  $url
	 * @param  bool    $https
	 * @return string
	 */
	public static function to_asset($url, $https = null)
	{
		if (is_null($https)) $https = Request::secure();

		return str_replace('index.php/', '', static::to($url, $https));
	}

	/**
	 * Generate a URL from a route name.
	 *
	 * For routes that have wildcard parameters, an array may be passed as the second
	 * parameter to the method. The values of this array will be used to fill the
	 * wildcard segments of the route URI.
	 *
	 * Optional parameters will be convereted to spaces if no parameter values are specified.
	 *
	 * <code>
	 *		// Generate the URL for a given route
	 *		echo URL::to_route('profile');
	 *
	 *		// Generate the URL for a given route with wildcard segments
	 *		echo URL::to_route('profile', array($username));
	 * </code>
	 *
	 * @param  string  $name
	 * @param  array   $parameters
	 * @param  bool    $https
	 * @return string
	 */
	public static function to_route($name, $parameters = array(), $https = false)
	{
		if ( ! is_null($route = IoC::container()->resolve('laravel.routing.router')->find($name)))
		{
			$uris = explode(', ', key($route));

			$uri = substr($uris[0], strpos($uris[0], '/'));

			// Spin through each route parameter and replace the route wildcard segment
			// with the corresponding parameter passed to the method.
			foreach ($parameters as $parameter)
			{
				$uri = preg_replace('/\(.+?\)/', $parameter, $uri, 1);
			}

			// Before generating the route URL, we will replace all remaining optional
			// wildcard segments that were not replaced by parameters with spaces.
			return static::to(str_replace(array('/(:any?)', '/(:num?)'), '', $uri), $https);
		}

		throw new \Exception("Error generating named route for route [$name]. Route is not defined.");
	}

	/**
	 * Generate a HTTPS URL from a route name.
	 *
	 * <code>
	 *		// Generate the URL for a route with HTTPS
	 *		echo URL::to_secure_route('profile');
	 *
	 *		// Generate the URL for a route with HTTPS and wildcard segments
	 *		echo URL::to_secure_route('profile', array($username));
	 * </code>
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
		$title = Str::ascii($title);

		// Remove all characters that are not the separator, letters, numbers, or whitespace.
		$title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', Str::lower($title));

		// Replace all separator characters and whitespace by a single separator
		$title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

		return trim($title, $separator);
	}

	/**
	 * Magic Method for dynamically creating URLs to named routes.
	 *
	 * <code>
	 *		// Generate the URL for the "profile" named route
	 *		echo URL::to_profile();
	 *
	 *		// Generate the URL for the "profile" named route with wildcard segments
	 *		echo URL::to_profile(array($username));
	 *
	 *		// Generate the URL for the "profile" named route with HTTPS
	 *		echo URL::to_secure_profile();
	 * </code>
	 */
	public static function __callStatic($method, $parameters)
	{
		$parameters = (isset($parameters[0])) ? $parameters[0] : array();

		if (strpos($method, 'to_secure_') === 0)
		{
			return static::to_route(substr($method, 10), $parameters, true);
		}

		if (strpos($method, 'to_') === 0)
		{
			return static::to_route(substr($method, 3), $parameters);
		}

		throw new \Exception("Method [$method] is not defined on the URL class.");
	}

}