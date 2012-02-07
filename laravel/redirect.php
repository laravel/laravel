<?php namespace Laravel;

class Redirect extends Response {

	/**
	 * Create a redirect response.
	 *
	 * <code>
	 *		// Create a redirect response to a location within the application
	 *		return Redirect::to('user/profile');
	 *
	 *		// Create a redirect response with a 301 status code
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
		return static::make('', $status)->header('Location', URL::to($url, $https));
	}

	/**
	 * Create a redirect response to a HTTPS URL.
	 *
	 * @param  string    $url
	 * @param  int       $status
	 * @return Redirect
	 */
	public static function to_secure($url, $status = 302)
	{
		return static::to($url, $status, true);
	}

	/**
	 * Create a redirect response to a named route.
	 *
	 * <code>
	 *		// Create a redirect response to the "login" named route
	 *		return Redirect::to_route('login');
	 *
	 *		// Create a redirect response to the "profile" named route with parameters
	 *		return Redirect::to_route('profile', array($username));
	 * </code>
	 *
	 * @param  string    $route
	 * @param  array     $parameters
	 * @param  int       $status
	 * @param  bool      $https
	 * @return Redirect
	 */
	public static function to_route($route, $parameters = array(), $status = 302, $https = false)
	{
		return static::to(URL::to_route($route, $parameters, $https), $status);
	}

	/**
	 * Create a redirect response to a named route using HTTPS.
	 *
	 * @param  string    $route
	 * @param  array     $parameters
	 * @param  int       $status
	 * @return Redirect
	 */
	public static function to_secure_route($route, $parameters = array(), $status = 302)
	{
		return static::to_route($route, $parameters, $status, true);
	}

	/**
	 * Add an item to the session flash data.
	 *
	 * This is useful for "passing" status messages or other data to the next request.
	 *
	 * <code>
	 *		// Create a redirect response and flash to the session
	 *		return Redirect::to('profile')->with('message', 'Welcome Back!');
	 * </code>
	 *
	 * @param  string          $key
	 * @param  mixed           $value
	 * @return Redirect
	 */
	public function with($key, $value)
	{
		if (Config::get('session.driver') == '')
		{
			throw new \Exception('A session driver must be set before setting flash data.');
		}

		Session::flash($key, $value);

		return $this;
	}

	/**
	 * Flash the old input to the session and return the Redirect instance.
	 *
	 * Once the input has been flashed, it can be retrieved via the Input::old method.
	 *
	 * <code>
	 *		// Redirect and flash all of the input data to the session
	 *		return Redirect::to('login')->with_input();
	 *
	 *		// Redirect and flash only a few of the input items
	 *		return Redirect::to('login')->with_input('only', array('email', 'username'));
	 *
	 *		// Redirect and flash all but a few of the input items
	 *		return Redirect::to('login')->with_input('except', array('password', 'ssn'));
	 * </code>
	 *
	 * @param  string    $filter
	 * @param  array     $items
	 * @return Redirect
	 */
	public function with_input($filter = null, $items = array())
	{
		Input::flash($filter, $items);

		return $this;
	}

	/**
	 * Flash a Validator's errors to the session data.
	 *
	 * This method allows you to conveniently pass validation errors back to views.
	 *
	 * <code>
	 *		// Redirect and flash validator errors the session
	 *		return Redirect::to('register')->with_errors($validator);
	 * </code>
	 *
	 * @param  Validator|Messages  $container
	 * @return Redirect
	 */
	public function with_errors($container)
	{
		$errors = ($container instanceof Validator) ? $container->errors : $container;

		return $this->with('errors', $errors);
	}

}