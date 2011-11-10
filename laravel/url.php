<?php namespace Laravel;

class URL {

	/**
	 * Generate an application URL.
	 *
	 * If the given URL is already well-formed, it will be returned unchanged.
	 *
	 * <code>
	 *		// Create a URL to a location within the application
	 *		$url = URL::to('user/profile');
	 *
	 *		// Create a HTTPS URL to a location within the application
	 *		$url = URL::to('user/profile', true);
	 * </code>
	 *
	 * @param  string  $url
	 * @param  bool    $https
	 * @return string
	 */
	public static function to($url = '', $https = false)
	{
		if (filter_var($url, FILTER_VALIDATE_URL) !== false) return $url;

		$root = Config::$items['application']['url'].'/'.Config::$items['application']['index'];

		if ($https and Config::$items['application']['ssl'])
		{
			$root = preg_replace('~http://~', 'https://', $root, 1);
		}

		return rtrim($root, '/').'/'.ltrim($url, '/');
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
	 * Generate an application URL to an asset.
	 *
	 * The index file will not be added to asset URLs. If the HTTPS option is not
	 * specified, HTTPS will be used when the active request is also using HTTPS.
	 *
	 * @param  string  $url
	 * @param  bool    $https
	 * @return string
	 */
	public static function to_asset($url, $https = null)
	{
		if (is_null($https)) $https = Request::secure();

		$url = static::to($url, $https);

		if (($index = Config::$items['application']['index']) !== '')
		{
			$url = str_replace($index.'/', '', $url);
		}

		return $url;
	}

	/**
	 * Generate a URL from a route name.
	 *
	 * For routes that have wildcard parameters, an array may be passed as the second
	 * parameter to the method. The values of this array will be used to fill the
	 * wildcard segments of the route URI.
	 *
	 * <code>
	 *		// Create a URL to the "profile" named route
	 *		$url = URL::to_route('profile');
	 *
	 *		// Create a URL to the "profile" named route with wildcard parameters
	 *		$url = URL::to_route('profile', array($username));
	 * </code>
	 *
	 * @param  string  $name
	 * @param  array   $parameters
	 * @param  bool    $https
	 * @return string
	 */
	public static function to_route($name, $parameters = array(), $https = false)
	{
		if ( ! is_null($route = IoC::container()->core('routing.router')->find($name)))
		{
			$uris = explode(', ', key($route));

			// Grab the first URI assigned to the route, removing the request URI
			// and leading slash from the destination defined on the route.
			$uri = substr($uris[0], strpos($uris[0], '/'));

			// Spin through each route parameter and replace the route wildcard
			// segment with the corresponding parameter passed to the method.
			foreach ((array) $parameters as $parameter)
			{
				$uri = preg_replace('/\(.+?\)/', $parameter, $uri, 1);
			}

			// Replace all remaining optional segments with spaces. Since the
			// segments are, obviously, optional, some of them may not have
			// been assigned values from the parameter array.
			return static::to(str_replace(array('/(:any?)', '/(:num?)'), '', $uri), $https);
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
	 * <code>
	 *		// Returns "this-is-my-blog-post"
	 *		$slug = URL::slug('This is my blog post!');
	 *
	 *		// Returns "this_is_my_blog_post"
	 *		$slug = URL::slug('This is my blog post!', '_');
	 * </code>
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
	 *		// Create a URL to the "profile" named route
	 *		$url = URL::to_profile();
	 *
	 *		// Create a URL to the "profile" named route with wildcard segments
	 *		$url = URL::to_profile(array($username));
	 *
	 *		// Create a URL to the "profile" named route using HTTPS
	 *		$url = URL::to_secure_profile();
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