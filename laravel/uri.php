<?php namespace Laravel;

class URI {

	/**
	 * The URI for the current request.
	 *
	 * This property will be set after the URI is detected for the first time.
	 *
	 * @var string
	 */
	protected static $uri;

	/**
	 * Determine the request URI.
	 *
	 * The request URI will be trimmed to remove to the application URL and application index file.
	 * If the request is to the root of the application, the URI will be set to a forward slash.
	 *
	 * If the $_SERVER "PATH_INFO" variable is available, it will be used; otherwise, we will try
	 * to determine the URI using the REQUEST_URI variable. If neither are available,  an exception
	 * will be thrown by the method.
	 *
	 * @return string
	 */
	public static function get()
	{
		if ( ! is_null(static::$uri)) return static::$uri;

		if (($uri = static::from_server()) === false)
		{
			throw new \Exception('Malformed request URI. Request terminated.');
		}

		return static::$uri = static::format(static::clean($uri));
	}

	/**
	 * Get a given URI segment from the URI for the current request.
	 *
	 * <code>
	 *		// Get the first segment from the request URI
	 *		$first = Request::uri()->segment(1);
	 *
	 *		// Get the second segment or return a default value if it doesn't exist
	 *		$second = Request::uri()->segment(2, 'Taylor');
	 *
	 *		// Get all of the segments for the request URI
	 *		$segments = Request::uri()->segment();
	 * </code>
	 *
	 * @param  int     $segment
	 * @param  mixed   $default
	 * @return string
	 */
	public static function segment($segment = null, $default = null)
	{
		$segments = Arr::without(explode('/', static::get()), array(''));

		if ( ! is_null($segment)) $segment = $segment - 1;

		return Arr::get($segments, $segment, $default);
	}

	/**
	 * Get the request URI from the $_SERVER array.
	 *
	 * @return string
	 */
	protected static function from_server()
	{
		// If the PATH_INFO $_SERVER element is set, we will use since it contains
		// the request URI formatted perfectly for Laravel's routing engine.
		if (isset($_SERVER['PATH_INFO']))
		{
			return $_SERVER['PATH_INFO'];
		}

		// If the REQUEST_URI is set, we need to extract the URL path since this
		// should return the URI formatted in a manner similar to PATH_INFO.
		elseif (isset($_SERVER['REQUEST_URI']))
		{
			return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		}

		throw new \Exception('Unable to determine the request URI.');
	}

	/**
	 * Remove extraneous segments from the URI such as the URL and index page.
	 *
	 * These segments need to be removed since they will cause problems in the
	 * routing engine if they are present in the URI.
	 *
	 * @param  string  $uri
	 * @return string
	 */
	protected static function clean($uri)
	{
		foreach (array(parse_url(Config::get('application.url'), PHP_URL_PATH), '/index.php') as $value)
		{
			$uri = (strpos($uri, $value) === 0) ? substr($uri, strlen($value)) : $uri;
		}

		return $uri;
	}

	/**
	 * Format the URI.
	 *
	 * If the request URI is empty, a single forward slash will be returned.
	 *
	 * @param  string  $uri
	 * @return string
	 */
	protected static function format($uri)
	{
		return (($uri = trim($uri, '/')) == '') ? '/' : $uri;
	}

}