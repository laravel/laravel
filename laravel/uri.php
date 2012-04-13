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
	 * Get the full URI including the query string.
	 *
	 * @return string
	 */
	public static function full()
	{
		return Request::getUri();
	}

	/**
	 * Get the URI for the current request.
	 *
	 * @return string
	 */
	public static function current()
	{
		if ( ! is_null(static::$uri)) return static::$uri;

		// We'll simply get the path info from the Symfony Request instance and then
		// format to meet our needs in the router. If the URI is root, we'll give
		// back a single slash, otherwise we'll strip the slashes.
		$uri = static::format(Request::getPathInfo());

		static::segments($uri);

		return static::$uri = $uri;
	}

	/**
	 * Format a given URI.
	 *
	 * @param  string  $uri
	 * @return string
	 */
	protected static function format($uri)
	{
		return trim($uri, '/') ?: '/';
	}

	/**
	 * Determine if the current URI matches a given pattern.
	 *
	 * @param  string  $pattern
	 * @return bool
	 */
	public static function is($pattern)
	{
		return Str::is($pattern, static::current());
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
	protected static function segments($uri)
	{
		$segments = explode('/', trim($uri, '/'));

		static::$segments = array_diff($segments, array(''));
	}

}