<?php namespace Laravel;

class Redirect_Facade extends Facade { public static $resolve = 'redirect'; }

class Redirect extends Response {

	/**
	 * The URL generator instance.
	 *
	 * @var URL
	 */
	private $url;

	/**
	 * Create a new redirect generator instance.
	 *
	 * @param  Session\Driver  $session
	 * @param  URL             $url
	 * @return void
	 */
	public function __construct(URL $url)
	{
		$this->url = $url;
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
	public function to($url, $status = 302, $method = 'location', $https = false)
	{
		$url = $this->url->to($url, $https);

		parent::__construct('', $status);

		if ($method == 'location')
		{
			return $this->header('Refresh', '0;url='.$url);
		}
		else
		{
			return $this->header('Location', $url);
		}
	}

	/**
	 * Create a redirect response to a HTTPS URL.
	 *
	 * @param  string    $url
	 * @param  int       $status
	 * @param  string    $method
	 * @return Response
	 */
	public function to_secure($url, $status = 302, $method = 'location')
	{
		return $this->to($url, $status, $method, true);
	}

	/**
	 * Add an item to the session flash data.
	 *
	 * This is useful for passing status messages or other temporary data to the next request.
	 *
	 * @param  string          $key
	 * @param  mixed           $value
	 * @return Response
	 */
	public function with($key, $value)
	{
		IoC::container()->resolve('laravel.session')->flash($key, $value);

		return $this;
	}

	/**
	 * Magic Method to handle creation of redirects to named routes.
	 */
	public function __call($method, $parameters)
	{
		$parameters = (isset($parameters[0])) ? $parameters[0] : array();

		if (strpos($method, 'to_secure_') === 0)
		{
			return $this->to($this->url->to_route(substr($method, 10), $parameters, true));
		}

		if (strpos($method, 'to_') === 0)
		{
			return $this->to($this->url->to_route(substr($method, 3), $parameters));
		}

		throw new \Exception("Method [$method] is not defined on the Redirect class.");
	}

}