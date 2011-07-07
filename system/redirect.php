<?php namespace System;

class Redirect {

	/**
	 * The redirect response.
	 *
	 * @var Response
	 */
	public $response;

	/**
	 * Create a new redirect instance.
	 *
	 * @param  Response  $response
	 * @return void
	 */
	public function __construct($response)
	{
		$this->response = $response;
	}

	/**
	 * Create a redirect response.
	 *
	 * @param  string    $url
	 * @param  string    $method
	 * @param  int       $status
	 * @param  bool      $https
	 * @return Response
	 */
	public static function to($url, $method = 'location', $status = 302, $https = false)
	{
		$url = URL::to($url, $https);

		return ($method == 'refresh')
							? new static(Response::make('', $status)->header('Refresh', '0;url='.$url))
							: new static(Response::make('', $status)->header('Location', $url));
	}

	/**
	 * Create a redirect response to a HTTPS URL.
	 *
	 * @param  string    $url
	 * @param  string    $method
	 * @param  int       $status
	 * @return Response
	 */
	public static function to_secure($url, $method = 'location', $status = 302)
	{
		return static::to($url, $method, $status, true);
	}

	/**
	 * Add an item to the session flash data.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return Response
	 */
	public function with($key, $value)
	{
		if (Config::get('session.driver') != '')
		{
			Session::flash($key, $value);
		}

		return $this;
	}

	/**
	 * Magic Method to handle redirecting to routes.
	 */
	public static function __callStatic($method, $parameters)
	{
		// Get the parameters for the method. Dynamic routes can be generated using an
		// array of parameters for routes that contain wildcards, such as /user/(:num).
		//
		//     Example: Redirect::to_profile(array(1));
		//
		// Here we'll check to see if a parameter was passed. If it wasn't, we'll just
		// pass an empty array into the URL generator.
		$parameters = (isset($parameters[0])) ? $parameters[0] : array();

		// Dynamically redirect to a secure route URL.
		if (strpos($method, 'to_secure_') === 0)
		{
			return static::to(URL::to_route(substr($method, 10), $parameters, true));
		}

		// Dynamically redirect a route URL.
		if (strpos($method, 'to_') === 0)
		{
			return static::to(URL::to_route(substr($method, 3), $parameters));
		}

		throw new \Exception("Method [$method] is not defined on the Redirect class.");
	}

}