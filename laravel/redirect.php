<?php namespace Laravel;

class Redirect extends Response {

	/**
	 * Create a redirect response.
	 *
	 * <code>
	 *		// Create a redirect response to a given URL
	 *		return Redirect::to('user/profile');
	 *
	 *		// Create a redirect with a given status code
	 *		return Redirect::to('user/profile', 301);
	 * </code>
	 *
	 * @param  string    $url
	 * @param  int       $status
	 * @param  bool      $https
	 * @return Redirect
	 */
	public static function to($url, $status = 302, $https = false)
	{
		$response = new static('', $status);

		return $response->header('Location', URL::to($url, $https));
	}

	/**
	 * Create a redirect response to a HTTPS URL.
	 *
	 * <code>
	 *		// Create a redirect response to a HTTPS URL
	 *		return Redirect::to_secure('user/profile');
	 * </code>
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
	 *		// Create a redirect and flash a messages to the session
	 *		return Redirect::to_profile()->with('message', 'Welcome Back!');
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
			throw new \Exception('A session driver must be set before setting flash data.');
		}

		IoC::container()->resolve('laravel.session')->flash($key, $value);

		return $this;
	}

	/**
	 * Magic Method to handle creation of redirects to named routes.
	 *
	 * <code>
	 *		// Create a redirect to the "profile" route
	 *		return Redirect::to_profile();
	 *
	 *		// Create a redirect to the "profile" route with wildcard segments
	 *		return Redirect::to_profile(array($username));
	 *
	 *		// Create a redirect to the "profile" route using HTTPS
	 *		return Redirect::to_secure_profile();
	 * </code>
	 */
	public static function __callStatic($method, $parameters)
	{
		$parameters = (isset($parameters[0])) ? $parameters[0] : array();

		if (strpos($method, 'to_secure_') === 0)
		{
			return static::to(URL::to_route(substr($method, 10), $parameters, true));
		}

		if (strpos($method, 'to_') === 0)
		{
			return static::to(URL::to_route(substr($method, 3), $parameters));
		}

		throw new \Exception("Method [$method] is not defined on the Redirect class.");
	}

}