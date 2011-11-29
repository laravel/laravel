<?php namespace Laravel;

class URI {

	/**
	 * The URI for the current request.
	 *
	 * @var string
	 */
	public static $uri;

	/**
	 * The URI segments for the current request.
	 *
	 * @var array
	 */
	protected static $segments = array();

	/**
	 * Get the URI for the current request.
	 *
	 * If the request is to the root of the application, a single forward slash
	 * will be returned. Otherwise, the URI will be returned with all of the
	 * leading and trailing slashes removed.
	 *
	 * @return string
	 */
	public static function current()
	{
		if ( ! is_null(static::$uri)) return static::$uri;

		$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

		// Remove the root application URL from the request URI. If the application
		// is nested within a sub-directory of the web document root, this will get
		// rid of all of the the sub-directories from the request URI.
		$uri = static::remove($uri, parse_url(URL::base(), PHP_URL_PATH));

		if (($index = '/'.Config::$items['application']['index']) !== '/')
		{
			$uri = static::remove($uri, $index);
		}

		static::$uri = static::format($uri);

		static::$segments = explode('/', static::$uri);

		return static::$uri;
	}

	/**
	 * Get a specific segment of the request URI via an one-based index.
	 *
	 * <code>
	 *		// Get the first segment of the request URI
	 *		$segment = URI::segment(1);
	 *
	 *		// Get the second segment of the URI, or return a default value
	 *		$segment = URI::segment(2, 'Taylor');
	 * </code>
	 *
	 * @param  int     $index
	 * @param  mixed   $default
	 * @return string
	 */
	public static function segment($index, $default = null)
	{
		static::current();

		return Arr::get(static::$segments, $index - 1, $default);
	}

	/**
	 * Remove a given value from the URI.
	 *
	 * @param  string  $uri
	 * @param  string  $value
	 * @return string
	 */
	protected static function remove($uri, $value)
	{
		if (strpos($uri, $value) === 0)
		{
			return substr($uri, strlen($value));
		}
		return $uri;
	}

	/**
	 * Format a given URI.
	 *
	 * If the URI is an empty string, a single forward slash will be returned.
	 * Otherwise, we will trim the URI's leading and trailing slashes.
	 *
	 * @param  string  $uri
	 * @return string
	 */
	protected static function format($uri)
	{
		return (($uri = trim($uri, '/')) !== '') ? $uri : '/';
	}

}