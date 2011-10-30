<?php namespace Laravel; use Closure;

class Request {

	/**
	 * The route handling the current request.
	 *
	 * @var Routing\Route
	 */
	public $route;

	/**
	 * The request URI for the current request.
	 *
	 * @var URI
	 */
	protected $uri;

	/**
	 * The $_POST array for the request.
	 *
	 * @var array
	 */
	protected $post;

	/**
	 * The $_SERVER array for the request.
	 *
	 * @var array
	 */
	protected $server;

	/**
	 * The request data key that is used to indicate a spoofed request method.
	 *
	 * @var string
	 */
	const spoofer = '__spoofer';

	/**
	 * Create a new Request instance.
	 *
	 * @param  URI    $uri
	 * @param  array  $post
	 * @param  array  $server
	 * @return void
	 */
	public function __construct($uri, $post, $server)
	{
		$this->uri = $uri;
		$this->post = $post;
		$this->server = $server;
	}

	/**
	 * Get the current request's URI.
	 *
	 * @return string
	 */
	public function uri()
	{
		return $this->uri->get();
	}

	/**
	 * Get the request method.
	 *
	 * This will usually be the value of the REQUEST_METHOD $_SERVER variable
	 * However, when the request method is spoofed using a hidden form value,
	 * the method will be stored in the $_POST array.
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
	 * @return bool
	 */
	public function spoofed()
	{
		return is_array($this->post) and array_key_exists(Request::spoofer, $this->post);
	}

	/**
	 * Get the requestor's IP address.
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

		return ($default instanceof Closure) ? call_user_func($default) : $default;
	}

	/**
	 * Get the HTTP protocol for the request.
	 *
	 * @return string
	 */
	public function protocol()
	{
		return Arr::get($this->server, 'SERVER_PROTOCOL', 'HTTP/1.1');
	}

	/**
	 * Determine if the current request is using HTTPS.
	 *
	 * @return bool
	 */
	public function secure()
	{
		return isset($this->server['HTTPS']) and strtolower($this->server['HTTPS']) !== 'off';
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
	public function route()
	{
		return $this->route;
	}

}