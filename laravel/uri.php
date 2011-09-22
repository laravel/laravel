<?php namespace Laravel;

class URI {

	/**
	 * The URI for the current request.
	 *
	 * This property will be set after the URI is detected for the first time.
	 *
	 * @var string
	 */
	protected $uri;

	/**
	 * The $_SERVER array for the current request.
	 *
	 * @var array
	 */
	protected $server;

	/**
	 * Create a new URI parser instance.
	 *
	 * @param  array  $server
	 * @return void
	 */
	public function __construct($server)
	{
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
	public function get()
	{
		if ( ! is_null($this->uri)) return $this->uri;

		if (($uri = $this->from_server()) === false)
		{
			throw new \Exception('Malformed request URI. Request terminated.');
		}

		return $this->uri = $this->format($this->clean($uri));
	}

	/**
	 * Get a given URI segment from the URI for the current request.
	 *
	 * @param  int     $segment
	 * @param  mixed   $default
	 * @return string
	 */
	public function segment($segment = null, $default = null)
	{
		$segments = Arr::without(explode('/', $this->get()), array(''));

		if ( ! is_null($segment)) $segment = $segment - 1;

		return Arr::get($segments, $segment, $default);
	}

	/**
	 * Get the request URI from the $_SERVER array.
	 *
	 * @return string
	 */
	protected function from_server()
	{
		// If the PATH_INFO $_SERVER element is set, we will use since it contains
		// the request URI formatted perfectly for Laravel's routing engine.
		if (isset($this->server['PATH_INFO']))
		{
			return $this->server['PATH_INFO'];
		}

		// If the REQUEST_URI is set, we need to extract the URL path since this
		// should return the URI formatted in a manner similar to PATH_INFO.
		elseif (isset($this->server['REQUEST_URI']))
		{
			return parse_url($this->server['REQUEST_URI'], PHP_URL_PATH);
		}

		throw new \Exception('Unable to determine the request URI.');
	}

	/**
	 * Remove extraneous segments from the URI such as the URL and index page.
	 *
	 * These segments need to be removed since they will cause problems in the
	 * routing engine if they are present in the URI.
	 *
	 * @param  string  $uri
	 * @return string
	 */
	protected function clean($uri)
	{
		foreach (array(parse_url(Config::get('application.url'), PHP_URL_PATH), '/index.php') as $value)
		{
			$uri = (strpos($uri, $value) === 0) ? substr($uri, strlen($value)) : $uri;
		}

		return $uri;
	}

	/**
	 * Format the URI.
	 *
	 * If the request URI is empty, a single forward slash will be returned.
	 *
	 * @param  string  $uri
	 * @return string
	 */
	protected function format($uri)
	{
		return (($uri = trim($uri, '/')) == '') ? '/' : $uri;
	}

}