<?php namespace Laravel;

class Request {

	/**
	 * The $_SERVER array for the request.
	 *
	 * @var array
	 */
	public $server;

	/**
	 * The $_POST array for the request.
	 *
	 * @var array
	 */
	private $post;

	/**
	 * The route handling the current request.
	 *
	 * @var Routing\Route
	 */
	public $route;

	/**
	 * The base URL of the application.
	 *
	 * @var string
	 */
	private $url;

	/**
	 * Create a new request instance.
	 *
	 * @param  array   $server
	 * @param  array   $post
	 * @param  string  $url
	 * @return void
	 */
	public function __construct($server, $post, $url)
	{
		$this->url = $url;
		$this->post = $post;
		$this->server = $server;
	}

	/**
	 * Determine the request URI.
	 *
	 * The request URI will be trimmed to remove to the application URL and application index file.
	 * If the request is to the root of the application, the URI will be set to a forward slash.
	 *
	 * If the $_SERVER "PATH_INFO" variable is available, it will be used; otherwise, we will try
	 * to determine the URI using the REQUEST_URI variable. If neither are available,  an exception
	 * will be thrown by the method.
	 *
	 * @return string
	 */
	public function uri()
	{
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

		foreach (array(parse_url($this->url, PHP_URL_PATH), '/index.php') as $value)
		{
			$uri = (strpos($uri, $value) === 0) ? substr($uri, strlen($value)) : $uri;
		}

		return (($uri = trim($uri, '/')) == '') ? '/' : $uri;
	}

	/**
	 * Get the request method.
	 *
	 * Typically, this will be the value of the REQUEST_METHOD $_SERVER variable.
	 * However, when the request is being spoofed by a hidden form value, the request
	 * method will be stored in the $_POST array.
	 *
	 * @return string
	 */
	public function method()
	{
		return ($this->spoofed()) ? $this->post['REQUEST_METHOD'] : $this->server['REQUEST_METHOD'];
	}

	/**
	 * Determine if the request method is being spoofed by a hidden Form element.
	 *
	 * Hidden elements are used to spoof PUT and DELETE requests since they are not supported by HTML forms.
	 *
	 * @return bool
	 */
	public function spoofed()
	{
		return is_array($this->post) and array_key_exists('REQUEST_METHOD', $this->post);
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
	 * Get the HTTP protocol for the request.
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
	public function secure()
	{
		return ($this->protocol() == 'https');
	}

	/**
	 * Determine if the request is an AJAX request.
	 *
	 * @return bool
	 */
	public function ajax()
	{
		if ( ! isset($this->server['HTTP_X_REQUESTED_WITH'])) return false;

		return strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
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