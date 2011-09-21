<?php namespace Laravel;

class Request {

	/**
	 * The URI for the current request.
	 *
	 * @var string
	 */
	protected $uri;

	/**
	 * The $_SERVER array for the request.
	 *
	 * @var array
	 */
	protected $server;

	/**
	 * The $_POST array for the request.
	 *
	 * @var array
	 */
	protected $post;

	/**
	 * The route handling the current request.
	 *
	 * @var Routing\Route
	 */
	public $route;

	/**
	 * The request data key that is used to indicate the spoofed request method.
	 *
	 * @var string
	 */
	const spoofer = '__spoofer';

	/**
	 * Create a new request instance.
	 *
	 * @param  string  $uri
	 * @param  array   $server
	 * @param  array   $post
	 * @return void
	 */
	public function __construct($uri, $server, $post)
	{
		$this->uri = $uri;
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
		return $this->uri;
	}

	/**
	 * Get the request format.
	 *
	 * The format is determined by essentially taking the "extension" of the URI.
	 *
	 * <code>
	 *		// Returns "html" for a request to "/user/profile"
	 *		$format = Request::format();
	 *
	 *		// Returns "json" for a request to "/user/profile.json"
	 *		$format = Request::format();
	 * </code>
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
		return ($this->spoofed()) ? $this->post[Request::spoofer] : $this->server['REQUEST_METHOD'];
	}

	/**
	 * Get an item from the $_SERVER array.
	 *
	 * Like most array retrieval methods, a default value may be specified.
	 *
	 * <code>
	 *		// Get an item from the $_SERVER array
	 *		$value = Request::server('http_x_requested_for');
	 *
	 *		// Get an item from the $_SERVER array or return a default value
	 *		$value = Request::server('http_x_requested_for', '127.0.0.1');
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return string
	 */
	public function server($key = null, $default = null)
	{
		return Arr::get($this->server, strtoupper($key), $default);
	}

	/**
	 * Determine if the request method is being spoofed by a hidden Form element.
	 *
	 * Hidden elements are used to spoof PUT and DELETE requests since they are not supported
	 * by HTML forms. If the request is being spoofed, Laravel considers the spoofed request
	 * method the actual request method throughout the framework.
	 *
	 * @return bool
	 */
	public function spoofed()
	{
		return is_array($this->post) and array_key_exists(Request::spoofer, $this->post);
	}

	/**
	 * Get the requestor's IP address.
	 *
	 * A default may be passed and will be returned in the event the IP can't be determined
	 *
	 * <code>
	 *		// Get the requestor's IP address
	 *		$ip = Request::ip();
	 *
	 *		// Get the requestor's IP address or return a default value
	 *		$ip = Request::ip('127.0.0.1');
	 * </code>
	 *
	 * @param  mixed   $default
	 * @return string
	 */
	public function ip($default = '0.0.0.0')
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

		return ($default instanceof \Closure) ? call_user_func($default) : $default;
	}

	/**
	 * Get the HTTP protocol for the request.
	 *
	 * This method will return either "https" or "http", depending on whether HTTPS
	 * is being used for the current request.
	 *
	 * @return string
	 */
	public function protocol()
	{
		return (isset($this->server['HTTPS']) and $this->server['HTTPS'] !== 'off') ? 'https' : 'http';
	}

	/**
	 * Determine if the current request is using HTTPS.
	 *
	 * @return bool
	 */
	public function secure()
	{
		return $this->protocol() == 'https';
	}

	/**
	 * Determine if the current request is an AJAX request.
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