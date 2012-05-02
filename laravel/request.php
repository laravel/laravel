<?php namespace Laravel; use Closure;

class Request {

	/**
	 * All of the route instances handling the request.
	 *
	 * @var array
	 */
	public static $route;

	/**
	 * The Symfony HttpFoundation Request instance.
	 *
	 * @var HttpFoundation\Request
	 */
	public static $foundation;

	/**
	 * The request data key that is used to indicate a spoofed request method.
	 *
	 * @var string
	 */
	const spoofer = '_method';

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
		$method = static::foundation()->getMethod();

		return ($method == 'HEAD') ? 'GET' : $method;
	}

	/**
	 * Get a header from the request.
	 *
	 * <code>
	 *		// Get a header from the request
	 *		$referer = Request::header('referer');
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public static function header($key, $default = null)
	{
		return array_get(static::foundation()->headers->all(), $key, $default);
	}

	/**
	 * Get all of the HTTP request headers.
	 *
	 * @return array
	 */
	public static function headers()
	{
		return static::foundation()->headers->all();
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
		return array_get(static::foundation()->server->all(), $key, $default);
	}

	/**
	 * Determine if the request method is being spoofed by a hidden Form element.
	 *
	 * @return bool
	 */
	public static function spoofed()
	{
		return ! is_null(static::foundation()->get(Request::spoofer));
	}

	/**
	 * Get the requestor's IP address.
	 *
	 * @param  mixed   $default
	 * @return string
	 */
	public static function ip($default = '0.0.0.0')
	{
		return value(static::foundation()->getClientIp(), $default);
	}

	/**
	 * Get the list of acceptable content types for the request.
	 *
	 * @return array
	 */
	public static function accept()
	{
		return static::foundation()->getAcceptableContentTypes();
	}

	/**
	 * Determine if the request accepts a given content type.
	 *
	 * @return bool
	 */
	public static function accepts($type)
	{
		return in_array($type, static::accept());
	}

	/**
	 * Get the languages accepted by the client's browser.
	 *
	 * @return array
	 */
	public static function languages()
	{
		return static::foundation()->getLanguages();
	}

	/**
	 * Determine if the current request is using HTTPS.
	 *
	 * @return bool
	 */
	public static function secure()
	{
		return static::foundation()->isSecure() and Config::get('application.ssl');
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
		return static::foundation()->isXmlHttpRequest();
	}

	/**
	 * Get the HTTP referrer for the request.
	 *
	 * @return string
	 */
	public static function referrer()
	{
		return static::foundation()->headers->get('referer');
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
		return static::foundation()->server->get('LARAVEL_ENV');
	}

	/**
	 * Set the Laravel environment for the current request.
	 *
	 * @param  string  $env
	 * @return void
	 */
	public static function set_env($env)
	{
		static::foundation()->server->set('LARAVEL_ENV', $env);
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
	 * Detect the current environment from an environment configuration.
	 *
	 * @param  array        $environments
	 * @param  string       $uri
	 * @return string|null
	 */
	public static function detect_env(array $environments, $uri)
	{
		foreach ($environments as $environment => $patterns)
		{
			// Essentially we just want to loop through each environment pattern
			// and determine if the current URI matches the pattern and if so
			// we will simply return the environment for that URI pattern.
			foreach ($patterns as $pattern)
			{
				if (Str::is($pattern, $uri))
				{
					return $environment;
				}
			}
		}
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

	/**
	 * Get the Symfony HttpFoundation Request instance.
	 *
	 * @return HttpFoundation\Request
	 */
	public static function foundation()
	{
		return static::$foundation;
	}

	/**
	 * Pass any other methods to the Symfony request.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::foundation(), $method), $parameters);
	}

}