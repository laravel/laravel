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
	public static $segments = array();

	/**
	 * Get the URI for the current request.
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

		// We'll also remove the application's index page as it is not used for at
		// all for routing and is totally unnecessary as far as the routing of
		// incoming requests to the framework is concerned.
		if (($index = '/'.Config::get('application.index')) !== '/')
		{
			$uri = static::remove($uri, $index);
		}

		static::segments(static::$uri = static::format($uri));

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

		return array_get(static::$segments, $index - 1, $default);
	}

	/**
	 * Set the URI segments for the request.
	 *
	 * @param  string  $uri
	 * @return void
	 */
	protected function segments($uri)
	{
		$segments = explode('/', trim($uri, '/'));

		static::$segments = array_diff($segments, array(''));
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
		return (strpos($uri, $value) === 0) ? substr($uri, strlen($value)) : $uri;
	}

	/**
	 * Format a given URI.
	 *
	 * @param  string  $uri
	 * @return string
	 */
	protected static function format($uri)
	{
		return (($uri = trim($uri, '/')) !== '') ? $uri : '/';
	}

}