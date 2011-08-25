<?php namespace Laravel;

class URL {

	/**
	 * Generate an application URL.
	 *
	 * If the given URL is already well-formed, it will be returned unchanged.
	 *
	 * @param  string  $url
	 * @param  bool    $https
	 * @return string
	 */
	public static function to($url = '', $https = false)
	{
		if (filter_var($url, FILTER_VALIDATE_URL) !== false) return $url;

		$base = Config::get('application.url').'/'.Config::get('application.index');

		if ($https and strpos($base, 'http://') === 0)
		{
			$base = 'https://'.substr($base, 7);
		}

		return rtrim($base, '/').'/'.trim($url, '/');
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
	 * The index file will not be added to asset URLs.
	 *
	 * @param  string  $url
	 * @return string
	 */
	public static function to_asset($url)
	{
		return str_replace('index.php/', '', static::to($url, Request::active()->is_secure()));
	}

	/**
	 * Generate a URL from a route name.
	 *
	 * For routes that have wildcard parameters, an array may be passed as the second parameter to the method.
	 * The values of this array will be used to fill the wildcard segments of the route URI.
	 *
	 * <code>
	 *		// Generate a URL for the "profile" named route
	 *		$url = URL::to_route('profile');
	 *
	 *		// Generate a URL for the "profile" named route with parameters.
	 *		$url = URL::to_route('profile', array('fred'));
	 * </code>
	 *
	 * @param  string  $name
	 * @param  array   $parameters
	 * @param  bool    $https
	 * @return string
	 */
	public static function to_route($name, $parameters = array(), $https = false)
	{
		if ( ! is_null($route = Routing\Finder::find($name, Routing\Loader::all())))
		{
			$uris = explode(', ', key($route));

			$uri = substr($uris[0], strpos($uris[0], '/'));

			foreach ($parameters as $parameter)
			{
				$uri = preg_replace('/\(.+?\)/', $parameter, $uri, 1);
			}

			$uri = str_replace(array('/(:any?)', '/(:num?)'), '', $uri);

			return static::to($uri, $https);
		}

		throw new \Exception("Error generating named route for route [$name]. Route is not defined.");
	}

	/**
	 * Generate a HTTPS URL from a route name.
	 *
	 * <code>
	 *		// Generate a HTTPS URL for the "profile" named route
	 *		$url = URL::to_secure_route('profile');
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
	 * <code>
	 *		// Returns "my-first-post"
	 *		$slug = URL::slug('My First Post!!');
	 *
	 *		// Returns "my_first_post"
	 *		$slug = URL::slug('My First Post!!', '_');
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
	 *		// Generate a URL for the "profile" named route
	 *		$url = URL::to_profile();
	 *
	 *		// Generate a URL for the "profile" named route using HTTPS
	 *		$url = URL::to_secure_profile();
	 *
	 *		// Generate a URL for the "profile" named route with parameters.
	 *		$url = URL::to_profile(array('fred'));
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