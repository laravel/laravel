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
	 * The server variables to check for the URI.
	 *
	 * @var array
	 */
	protected static $attempt = array('PATH_INFO', 'REQUEST_URI', 'PHP_SELF', 'REDIRECT_URL');

	/**
	 * Get the URI for the current request.
	 *
	 * @return string
	 */
	public static function current()
	{
		if ( ! is_null(static::$uri)) return static::$uri;

		// To get the URI, we'll first call the detect method which will spin
		// through each of the server variables that we check for the URI in
		// and use the first one we encounter for the URI.
		static::$uri = static::detect();

		// If you ever encounter this error, please information the Laravel
		// dev team with information about your server. We want to support
		// Laravel an as many server environments as possible!
		if (is_null(static::$uri))
		{
			throw new \Exception("Could not detect request URI.");
		}

		static::segments(static::$uri);

		return static::$uri;
	}

	/**
	 * Detect the URI from the server variables.
	 *
	 * @return string|null
	 */
	protected static function detect()
	{
		foreach (static::$attempt as $variable)
		{
			// Each variable we search for the URI has its own parser function
			// which is responsible for doing any formatting before the value
			// is fed into the main formatting function.
			$method = "parse_{$variable}";

			if (isset($_SERVER[$variable]))
			{
				$uri = static::$method($_SERVER[$variable]);

				return static::format($uri);
			}
		}		
	}

	/**
	 * Format a given URI.
	 *
	 * @param  string  $uri
	 * @return string
	 */
	protected static function format($uri)
	{
		// First we want to remove the application's base URL from the URI
		// if it is in the string. It is possible for some of the server
		// variables to include the entire document root.
		$uri = static::remove_base($uri);

		$index = '/'.Config::get('application.index');

		// Next we'll remove the index file from the URI if it is there
		// and then finally trim down the URI. If the URI is left with
		// nothing but spaces, we use a single slash for root.
		if ($index !== '/')
		{
			$uri = static::remove($uri, $index);
		}

		return (($uri = trim($uri, '/')) !== '') ? $uri : '/';
	}

	/**
	 * Parse the PATH_INFO server variable.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function parse_path_info($value)
	{
		return $value;
	}

	/**
	 * Parse the REQUEST_URI server variable.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function parse_request_uri($value)
	{
		return parse_url($value, PHP_URL_PATH);
	}

	/**
	 * Parse the PHP_SELF server variable.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function parse_php_self($value)
	{
		return $value;
	}

	/**
	 * Parse the REDIRECT_URL server variable.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected static function parse_redirect_url($value)
	{
		return $value;
	}

	/**
	 * Remove the base URL off of the request URI.
	 *
	 * @param  string  $uri
	 * @return string
	 */
	protected static function remove_base($uri)
	{
		return static::remove($uri, parse_url(URL::base(), PHP_URL_PATH));
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

}