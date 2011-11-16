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
	 * If the request is to the root of the application, a single forward slash
	 * will be returned. Otherwise, the URI will be returned with all of the
	 * leading and trailing slashes removed.
	 *
	 * @return string
	 */
	public static function uri()
	{
		return URI::current();
	}

	/**
	 * Get the request method.
	 *
	 * This will usually be the value of the REQUEST_METHOD $_SERVER variable
	 * However, when the request method is spoofed using a hidden form value,
	 * the method will be stored in the $_POST array.
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
	 * @return string
	 */
	public static function protocol()
	{
		return Arr::get($_SERVER, 'SERVER_PROTOCOL', 'HTTP/1.1');
	}

	/**
	 * Determine if the current request is using HTTPS.
	 *
	 * @return bool
	 */
	public static function secure()
	{
		return isset($_SERVER['HTTPS']) and strtolower($_SERVER['HTTPS']) !== 'off';
	}

	/**
	 * Determine if the request has been forged.
	 *
	 * The session CSRF token will be compared to the CSRF token in the request input.
	 *
	 * @return bool
	 */
	public static function forged()
	{
		return Input::get('csrf_token') !== IoC::core('session')->token();
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
	public static function route()
	{
		return static::$route;
	}

}
