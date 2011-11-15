<?php namespace Laravel;

class Redirect extends Response {

	/**
	 * Create a redirect response.
	 *
	 * <code>
	 *		// Create a redirect response to a location within the application
	 *		return Redirect::to('user/profile');
	 *
	 *		// Create a redirect with a 301 status code
	 *		return Redirect::to('user/profile', 301);
	 *
	 *		// Create a redirect response to a location outside of the application
	 *		return Redirect::to('http://google.com');
	 * </code>
	 *
	 * @param  string    $url
	 * @param  int       $status
	 * @param  bool      $https
	 * @return Redirect
	 */
	public static function to($url, $status = 302, $https = false)
	{
		return static::make('', $status)->header('Location', URL::to($url, $https));
	}

	/**
	 * Create a redirect response to a HTTPS URL.
	 *
	 * @param  string    $url
	 * @param  int       $status
	 * @return Response
	 */
	public static function to_secure($url, $status = 302)
	{
		return static::to($url, $status, true);
	}

	/**
	 * Add an item to the session flash data.
	 *
	 * This is useful for passing status messages or other temporary data to the next request.
	 *
	 * <code>
	 *		// Create a redirect response and flash something to the session
	 *		return Redirect::to('user/profile')->with('message', 'Welcome Back!');
	 * </code>
	 *
	 * @param  string          $key
	 * @param  mixed           $value
	 * @return Response
	 */
	public function with($key, $value)
	{
		if (Config::get('session.driver') == '')
		{
			throw new \LogicException('A session driver must be set before setting flash data.');
		}

		IoC::core('session')->flash($key, $value);

		return $this;
	}

	/**
	 * Magic Method to handle creation of redirects to named routes.
	 *
	 * <code>
	 *		// Create a redirect response to the "profile" named route
	 *		return Redirect::to_profile();
	 *
	 *		// Create a redirect response to a named route using HTTPS
	 *		return Redirect::to_secure_profile();
	 * </code>
	 */
	public static function __callStatic($method, $parameters)
	{
		$status = (isset($parameters[1])) ? $parameters[1] : 302;
		
		$parameters = (isset($parameters[0])) ? $parameters[0] : array();

		if (strpos($method, 'to_secure_') === 0)
		{
			return static::to(URL::to_route(substr($method, 10), $parameters, true), $status);
		}

		if (strpos($method, 'to_') === 0)
		{
			return static::to(URL::to_route(substr($method, 3), $parameters), $status);
		}

		throw new \BadMethodCallException("Method [$method] is not defined on the Redirect class.");
	}

}
