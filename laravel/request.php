<?php namespace Laravel; use Closure;

class Request {

	/**
	 * All of the route instances handling the request.
	 *
	 * @var array
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
	 * @return string
	 */
	public static function uri()
	{
		return URI::current();
	}

	/**
	 * Get the request method.
	 *
	 * @return string
	 */
	public static function method()
	{
		if ($_SERVER['REQUEST_METHOD'] == 'HEAD')
		{
			return 'GET';
		}

		return (static::spoofed()) ? $_POST[Request::spoofer] : $_SERVER['REQUEST_METHOD'];
	}

	/**
	 * Get an item from the $_SERVER array.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return string
	 */
	public static function server($key = null, $default = null)
	{
		return array_get($_SERVER, strtoupper($key), $default);
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

		return value($default);
	}

	/**
	 * Get the HTTP protocol for the request.
	 *
	 * @return string
	 */
	public static function protocol()
	{
		return array_get($_SERVER, 'SERVER_PROTOCOL', 'HTTP/1.1');
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
		return Input::get(Session::csrf_token) !== Session::token();
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
	 * Get the HTTP referrer for the request.
	 *
	 * @return string
	 */
	public static function referrer()
	{
		return array_get($_SERVER, 'HTTP_REFERER');
	}

	/**
	 * Determine if the current request is via the command line.
	 *
	 * @return bool
	 */
	public static function cli()
	{
		return defined('STDIN');
	}

	/**
	 * Get the Laravel environment for the current request.
	 *
	 * @return string|null
	 */
	public static function env()
	{
		if (isset($_SERVER['LARAVEL_ENV'])) return $_SERVER['LARAVEL_ENV'];
	}

	/**
	 * Determine the current request environment.
	 *
	 * @param  string  $env
	 * @return bool
	 */
	public static function is_env($env)
	{
		return static::env() === $env;
	}

	/**
	 * Get the main route handling the request.
	 *
	 * @return Route
	 */
	public static function route()
	{
		return static::$route;
	}

}