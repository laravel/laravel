<?php namespace Laravel;

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
	 * @param  int       $status
	 * @param  string    $method
	 * @param  bool      $https
	 * @return Redirect
	 */
	public static function to($url, $status = 302, $method = 'location', $https = false)
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
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return Response
	 */
	public function with($key, $value)
	{
		if (Config::get('session.driver') == '')
		{
			throw new \Exception("Attempting to flash data to the session, but no session driver has been specified.");
		}

		Session::flash($key, $value);

		return $this;
	}

	/**
	 * Magic Method to handle redirecting to routes.
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