<?php namespace Laravel;

class URL {

	/**
	 * Get the base URL of the application.
	 *
	 * If the application URL is explicitly defined in the application configuration
	 * file, that URL will be returned. Otherwise, the URL will be guessed based on
	 * the host and script name available in the global $_SERVER array.
	 *
	 * @return string
	 */
	public static function base()
	{
		if (($base = Config::get('application.url')) !== '') return $base;

		if (isset($_SERVER['HTTP_HOST']))
		{
			$protocol = (Request::secure()) ? 'https://' : 'http://';

			// By removing the basename of the script, we should be left with the path where
			// the framework is installed. For example, if the framework is installed within
			// http://localhost/laravel/public, the path we'll get from this statement will
			// be "/laravel/public", and we remove that to get the base URL.
			$path = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

			return rtrim($protocol.$_SERVER['HTTP_HOST'].$path, '/');
		}

		return 'http://localhost';
	}

	/**
	 * Generate an application URL.
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

		$root = static::base().'/'.Config::get('application.index');

		// Since SSL is not often used while developing the application, we allow the
		// developer to disable SSL on all framework generated links to make it more
		// convenient to work with the site while developing locally.
		if ($https and Config::get('application.ssl'))
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
	 * @param  string  $url
	 * @param  bool    $https
	 * @return string
	 */
	public static function to_asset($url, $https = null)
	{
		if (is_null($https)) $https = Request::secure();

		$url = static::to($url, $https);

		// Since assets are not served by Laravel, we do not need to come through
		// the front controller. So, we'll remove the application index specified
		// in the application configuration from the generated URL.
		if (($index = Config::get('application.index')) !== '')
		{
			$url = str_replace($index.'/', '', $url);
		}

		return $url;
	}

	/**
	 * Generate a URL from a route name.
	 *
	 * For routes that have wildcard parameters, an array may be passed as the
	 * second parameter to the method. The values of this array will be used to
	 * fill the wildcard segments of the route URI.
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
		if ( ! is_null($route = Routing\Router::find($name)))
		{
			throw new \Exception("Error creating URL for undefined route [$name].");
		}

		$uris = explode(', ', key($route));

		// Routes can handle more than one URI, but we will just take the first URI
		// and use it for the URL. Since all of the URLs should point to the same
		// route, it doesn't make a difference.
		$uri = substr($uris[0], strpos($uris[0], '/'));

		// Spin through each route parameter and replace the route wildcard segment
		// with the corresponding parameter passed to the method. Afterwards, we'll
		// replace all of the remaining optional URI segments with spaces since
		// they may not have been specified in the array of parameters.
		foreach ((array) $parameters as $parameter)
		{
			$uri = preg_replace('/\(.+?\)/', $parameter, $uri, 1);
		}

		// If there are any remaining optional place-holders, we will just replace
		// them with empty strings since not every optional parameter has to be
		// in the array of parameters that were passed into the method.
		$uri = str_replace(array('/(:any?)', '/(:num?)'), '', $uri);

		return static::to($uri, $https);
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

}