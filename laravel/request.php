<?php namespace Laravel;

class Request {

	/**
	 * The request instance for the current request.
	 *
	 * @var Request
	 */
	private static $active;

	/**
	 * The $_SERVER array for the request.
	 *
	 * @var array
	 */
	private $server;

	/**
	 * The input instance for the request.
	 *
	 * @var Input
	 */
	public $input;

	/**
	 * The route handling the current request.
	 *
	 * @var Routing\Route
	 */
	public $route;

	/**
	 * The request URI.
	 *
	 * @var string
	 */
	private $uri;

	/**
	 * Create a new request instance.
	 *
	 * @param  array  $server
	 * @return void
	 */
	public function __construct($server)
	{
		$this->server = $server;

		static::$active = $this;
	}

	/**
	 * Get the request instance for the current request.
	 *
	 * @return Request
	 */
	public static function active()
	{
		return static::$active;
	}

	/**
	 * Get the raw request URI.
	 *
	 * @return string
	 */
	public function uri()
	{
		if ( ! is_null($this->uri)) return $this->uri;

		if (isset($this->server['PATH_INFO']))
		{
			$uri = $this->server['PATH_INFO'];
		}
		elseif (isset($this->server['REQUEST_URI']))
		{
			$uri = parse_url($this->server['REQUEST_URI'], PHP_URL_PATH);
		}
		else
		{
			throw new \Exception('Unable to determine the request URI.');
		}

		if ($uri === false) throw new \Exception('Malformed request URI. Request terminated.');

		return $this->uri = $this->remove_from_uri($uri, array(parse_url(Config::get('application.url'), PHP_URL_PATH), '/index.php'));
	}

	/**
	 * Remove an array of values from the beginning from a URI.
	 *
	 * @param  string  $uri
	 * @param  array   $values
	 * @return string
	 */
	private function remove_from_uri($uri, $values)
	{
		foreach ($values as $value)
		{
			$uri = (strpos($uri, $value) === 0) ? substr($uri, strlen($value)) : $uri;
		}
		
		return $uri;
	}

	/**
	 * Get the request method.
	 *
	 * Note: If the request method is being spoofed, the spoofed method will be returned.
	 *
	 * @return string
	 */
	public function method()
	{
		return ($this->is_spoofed()) ? $_POST['REQUEST_METHOD'] : $this->server['REQUEST_METHOD'];
	}

	/**
	 * Determine if the request method is being spoofed by a hidden Form element.
	 *
	 * Hidden elements are used to spoof PUT and DELETE requests since they are not supported by HTML forms.
	 *
	 * @return bool
	 */
	public function is_spoofed()
	{
		return is_array($_POST) and array_key_exists('REQUEST_METHOD', $_POST);
	}

	/**
	 * Get the requestor's IP address.
	 *
	 * @return string
	 */
	public function ip()
	{
		if (isset($this->server['HTTP_X_FORWARDED_FOR']))
		{
			return $this->server['HTTP_X_FORWARDED_FOR'];
		}
		elseif (isset($this->server['HTTP_CLIENT_IP']))
		{
			return $this->server['HTTP_CLIENT_IP'];
		}
		elseif (isset($this->server['REMOTE_ADDR']))
		{
			return $this->server['REMOTE_ADDR'];
		}
	}

	/**
	 * Get the HTTP protocol for the request (http or https).
	 *
	 * @return string
	 */
	public function protocol()
	{
		return (isset($this->server['HTTPS']) and $this->server['HTTPS'] !== 'off') ? 'https' : 'http';
	}

	/**
	 * Determine if the request is using HTTPS.
	 *
	 * @return bool
	 */
	public function is_secure()
	{
		return ($this->protocol() == 'https');
	}

	/**
	 * Determine if the request is an AJAX request.
	 *
	 * @return bool
	 */
	public function is_ajax()
	{
		return (isset($this->server['HTTP_X_REQUESTED_WITH']) and strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
	}

	/**
	 * Determine if the route handling the request has a given name.
	 *
	 * <code>
	 *		// Determine if the route handling the request is named "profile"
	 *		$profile = Request::active()->route_is('profile');
	 * </code>
	 *
	 * @param  string  $name
	 * @return bool
	 */
	public function route_is($name)
	{
		if (is_null($this->route) or ! is_array($this->route->callback) or ! isset($this->route->callback['name'])) return false;

		return $this->route->callback['name'] === $name;
	}

	/**
	 * Magic Method to handle dynamic method calls to determine the route handling the request.
	 *
	 * <code>
	 *		// Determine if the route handling the request is named "profile"
	 *		$profile = Request::active()->route_is_profile();
	 * </code>
	 */
	public function __call($method, $parameters)
	{
		if (strpos($method, 'route_is_') === 0)
		{
			return $this->route_is(substr($method, 9));
		}
	}

}