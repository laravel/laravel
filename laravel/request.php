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
	protected $post;

	/**
	 * The base URL of the application.
	 *
	 * @var string
	 */
	protected $url;

	/**
	 * The request URI.
	 *
	 * After determining the URI once, this property will be set and returned
	 * on subsequent requests for the URI.
	 *
	 * @var string
	 */
	protected $uri;

	/**
	 * The route handling the current request.
	 *
	 * @var Routing\Route
	 */
	public $route;

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

		if ($uri === false)
		{
			throw new \Exception('Malformed request URI. Request terminated.');
		}

		foreach (array(parse_url($this->url, PHP_URL_PATH), '/index.php') as $value)
		{
			$uri = (strpos($uri, $value) === 0) ? substr($uri, strlen($value)) : $uri;
		}

		return $this->uri = (($uri = trim($uri, '/')) == '') ? '/' : $uri;
	}

	/**
	 * Get the request format.
	 *
	 * The format is determined by essentially taking the "extension" of the URI.
	 *
	 * @return string
	 */
	public function format()
	{
		return (($extension = pathinfo($this->uri(), PATHINFO_EXTENSION)) !== '') ? $extension : 'html';
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
		return ($this->spoofed()) ? $this->post['_REQUEST_METHOD'] : $this->server['REQUEST_METHOD'];
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
	 * Get the route handling the current request.
	 *
	 * @return Route
	 */
	public function route() { return $this->route; }

}