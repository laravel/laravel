<?php namespace Laravel;

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
	 * @param  URL   $url
	 * @return void
	 */
	public function __construct(URL $url)
	{
		$this->url = $url;
	}

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
	public function to($url, $status = 302, $https = false)
	{
		parent::__construct('', $status);

		return $this->header('Location', $this->url->to($url, $https));
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
	public function to_secure($url, $status = 302)
	{
		return $this->to($url, $status, true);
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
		if (IoC::container()->resolve('laravel.config')->get('session.driver') == '')
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