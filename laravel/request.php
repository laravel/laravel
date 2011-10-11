<?php namespace Laravel; use Closure;

class Request {

	/**
	 * The route handling the current request.
	 *
	 * @var Routing\Route
	 */
	public static $route;

	/**
	 * The request URI for the current request.
	 *
	 * @var string
	 */
	public static $uri;

	/**
	 * The request data key that is used to indicate a spoofed request method.
	 *
	 * @var string
	 */
	const spoofer = '__spoofer';

	/**
	 * Get the URI for the current request.
	 *
	 * Note: This method is the equivalent of calling the URI::get method.
	 *
	 * @return string
	 */
	public static function uri()
	{
		if ( ! is_null(static::$uri)) return static::$uri;

		// Sniff the request URI out of the $_SERVER array. The PATH_IFNO
		// variable contains the URI without the base URL or index page,
		// so we will use that if possible, otherwise we will parse the
		// URI out of the REQUEST_URI element.
		if (isset($_SERVER['PATH_INFO']))
		{
			$uri = $_SERVER['PATH_INFO'];
		}
		elseif (isset($_SERVER['REQUEST_URI']))
		{
			$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

			if ($uri === false)
			{
				throw new \Exception("Invalid request URI. Request terminated.");
			}
		}
		else
		{
			throw new \Exception("Unable to determine the request URI.");
		}

		// Remove the application URL and the application index page from
		// the request URI. Both of these elements will interfere with the
		// routing engine and are extraneous, so they will be removed.
		$base = parse_url(Config::$items['application']['url'], PHP_URL_PATH);

		if (strpos($uri, $base) === 0)
		{
			$uri = substr($uri, strlen($base));
		}

		if (strpos($uri, '/index.php') === 0)
		{
			$uri = substr($uri, 10);
		}

		// Request URIs to the root of the application will be returned
		// as a single forward slash. Otherwise, the request URI will be
		// returned without leading or trailing slashes.
		return static::$uri = (($uri = trim($uri, '/')) == '') ? '/' : $uri;
	}

	/**
	 * Get the request format.
	 *
	 * The format is determined by essentially taking the "extension" of the URI.
	 *
	 * @return string
	 */
	public static function format()
	{
		return (($extension = pathinfo(static::uri(), PATHINFO_EXTENSION)) !== '') ? $extension : 'html';
	}

	/**
	 * Get the request method.
	 *
	 * Typically, this will be the value of the REQUEST_METHOD $_SERVER variable.
	 * However, when the request is being spoofed by a hidden form value, the request
	 * method will be stored in the $_POST array.
	 *
	 * @return string
	 */
	public static function method()
	{
		return (static::spoofed()) ? $_POST[Request::spoofer] : $_SERVER['REQUEST_METHOD'];
	}

	/**
	 * Get an item from the $_SERVER array.
	 *
	 * Like most array retrieval methods, a default value may be specified.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return string
	 */
	public static function server($key = null, $default = null)
	{
		return Arr::get($_SERVER, strtoupper($key), $default);
	}

	/**
	 * Determine if the request method is being spoofed by a hidden Form element.
	 *
	 * Hidden elements are used to spoof PUT and DELETE requests since they are not supported
	 * by HTML forms. If the request is being spoofed, Laravel considers the spoofed request
	 * method the actual request method throughout the framework.
	 *
	 * @return bool
	 */
	public static function spoofed()
	{
		return is_array($_POST) and array_key_exists(Request::spoofer, $_POST);
	}

	/**
	 * Get the requestor's IP address.
	 *
	 * @param  mixed   $default
	 * @return string
	 */
	public static function ip($default = '0.0.0.0')
	{
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		elseif (isset($_SERVER['HTTP_CLIENT_IP']))
		{
			return $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (isset($_SERVER['REMOTE_ADDR']))
		{
			return $_SERVER['REMOTE_ADDR'];
		}

		return ($default instanceof Closure) ? call_user_func($default) : $default;
	}

	/**
	 * Get the HTTP protocol for the request.
	 *
	 * This method will return either "https" or "http", depending on whether HTTPS
	 * is being used for the current request.
	 *
	 * @return string
	 */
	public static function protocol()
	{
		return (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
	}

	/**
	 * Determine if the current request is using HTTPS.
	 *
	 * @return bool
	 */
	public static function secure()
	{
		return static::protocol() == 'https';
	}

	/**
	 * Determine if the current request is an AJAX request.
	 *
	 * @return bool
	 */
	public static function ajax()
	{
		if ( ! isset($_SERVER['HTTP_X_REQUESTED_WITH'])) return false;

		return strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
	}

	/**
	 * Get the route handling the current request.
	 *
	 * @return Route
	 */
	public static function route() { return static::$route; }

}