<?php namespace Laravel; use Closure;

class Request {

	/**
	 * The route handling the current request.
	 *
	 * @var Routing\Route
	 */
	public static $route;

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
		return URI::get();
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
		return (($extension = pathinfo(URI::get(), PATHINFO_EXTENSION)) !== '') ? $extension : 'html';
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