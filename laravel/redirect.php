<?php namespace Laravel;

class Redirect extends Response {

	/**
	 * Create a redirect response.
	 *
	 * <code>
	 *		// Create a redirect for the "user/profile" URI
	 *		return Redirect::to('user/profile');
	 *
	 *		// Create a redirect using the 301 status code
	 *		return Redirect::to('user/profile', 301);
	 *
	 *		// Create a redirect using the "refresh" method
	 *		return Redirect::to('user/profile', 302, 'refresh');
	 * </code>
	 *
	 * @param  string    $url
	 * @param  int       $status
	 * @param  string    $method
	 * @param  bool      $https
	 * @return Redirect
	 */
	public static function to($url, $status = 302, $method = 'location', $https = false)
	{
		$url = IoC::container()->resolve('laravel.url')->to($url, $https);

		if ($method == 'location')
		{
			return static::make('', $status)->header('Refresh', '0;url='.$url);
		}
		else
		{
			return static::make('', $status)->header('Location', $url);
		}
	}

	/**
	 * Create a redirect response to a HTTPS URL.
	 *
	 * <code>
	 *		// Create a HTTPS redirect to the "user/profile" URI
	 *		return Redirect::to_secure('user/profile');
	 * </code>
	 *
	 * @param  string    $url
	 * @param  int       $status
	 * @param  string    $method
	 * @return Response
	 */
	public static function to_secure($url, $status = 302, $method = 'location')
	{
		return static::to($url, $status, $method, true);
	}

	/**
	 * Add an item to the session flash data.
	 *
	 * This is useful for passing status messages or other temporary data to the next request.
	 *
	 * <code>
	 *		// Flash a status message to the session on a redirect
	 *		return Redirect::to('user/profile')->with('status', 'Welcome Back!');
	 * </code>
	 *
	 * @param  string          $key
	 * @param  mixed           $value
	 * @param  Session\Driver  $driver
	 * @return Response
	 */
	public function with($key, $value, Session\Driver $driver)
	{
		if (is_null($driver)) $driver = Session::driver();

		$driver->flash($key, $value);

		return $this;
	}

	/**
	 * Magic Method to handle redirecting to named routes.
	 *
	 * <code>
	 *		// Create a redirect to the "profile" route
	 *		return Redirect::to_profile();
	 *
	 *		// Create a redirect to the "profile" route using HTTPS
	 *		return Redirect::to_secure_profile();
	 * </code>
	 */
	public static function __callStatic($method, $parameters)
	{
		$parameters = (isset($parameters[0])) ? $parameters[0] : array();

		$url = IoC::container()->resolve('laravel.url');

		if (strpos($method, 'to_secure_') === 0)
		{
			return static::to($url->to_route(substr($method, 10), $parameters, true));
		}

		if (strpos($method, 'to_') === 0)
		{
			return static::to($url->to_route(substr($method, 3), $parameters));
		}

		throw new \Exception("Method [$method] is not defined on the Redirect class.");
	}

}