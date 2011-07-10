<?php namespace System;

class Request {

	/**
	 * The route handling the current request.
	 *
	 * @var Route
	 */
	public static $route;

	/**
	 * Get the request URI.
	 *
	 * The server PATH_INFO will be used if available. Otherwise, the REQUEST_URI will be used.
	 * The application URL and index will be removed from the URI.
	 *
	 * If the request is to the root of application, a single forward slash will be returned.
	 *
	 * @return string
	 */
	public static function uri()
	{
		if (isset($_SERVER['PATH_INFO']))
		{
			$uri = $_SERVER['PATH_INFO'];
		}
		elseif (isset($_SERVER['REQUEST_URI']))
		{
			$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		}
		else
		{
			throw new \Exception('Unable to determine the request URI.');
		}

		if ($uri === false)
		{
			throw new \Exception("Malformed request URI. Request terminated.");
		}

		// Remove the application URL from the URI.
		if (strpos($uri, $base = parse_url(Config::get('application.url'), PHP_URL_PATH)) === 0)
		{
			$uri = substr($uri, strlen($base));
		}

		// Remove the application index page from the URI.
		if (strpos($uri, $index = '/'.Config::get('application.index')) === 0)
		{
			$uri = substr($uri, strlen($index));
		}

		return (($uri = trim($uri, '/')) == '') ? '/' : strtolower($uri);
	}

	/**
	 * Get the request method.
	 *
	 * @return string
	 */
	public static function method()
	{
		return (static::spoofed()) ? $_POST['REQUEST_METHOD'] : $_SERVER['REQUEST_METHOD'];
	}

	/**
	 * Determine if the request method is being spoofed by a hidden Form element.
	 *
	 * Hidden form elements are used to spoof PUT and DELETE requests since
	 * they are not supported by HTML forms.
	 *
	 * @return bool
	 */
	public static function spoofed()
	{
		return is_array($_POST) and array_key_exists('REQUEST_METHOD', $_POST);
	}

	/**
	 * Get the requestor's IP address.
	 *
	 * @return string
	 */
	public static function ip()
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
	}

	/**
	 * Get the HTTP protocol for the request.
	 *
	 * @return string
	 */
	public static function protocol()
	{
		return (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
	}

	/**
	 * Determine if the request is using HTTPS.
	 *
	 * @return bool
	 */
	public static function is_secure()
	{
		return (static::protocol() == 'https');
	}

	/**
	 * Determine if the request is an AJAX request.
	 *
	 * @return bool
	 */
	public static function is_ajax()
	{
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
	}

	/**
	 * Determine if the route handling the request is a given name.
	 *
	 * @param  string  $name
	 * @return bool
	 */
	public static function route_is($name)
	{
		return (is_array(static::$route->callback) and isset(static::$route->callback['name']) and  static::$route->callback['name'] === $name);
	}

	/**
	 * Magic Method to handle dynamic static methods.
	 */
	public static function __callStatic($method, $parameters)
	{
		// Dynamically determine if a given route is handling the request.
		if (strpos($method, 'route_is_') === 0)
		{
			return static::route_is(substr($method, 9));
		}
	}

}