<?php namespace Laravel;

class URI {

	/**
	 * The request URI for the current request.
	 *
	 * @var string
	 */
	protected $uri;

	/**
	 * The $_SERVER global array for the current request.
	 *
	 * @var array
	 */
	protected $server;

	/**
	 * Create a new instance of the URI class.
	 *
	 * @param  array  $server
	 * @return void
	 */
	public function __construct($server)
	{
		$this->server = $server;
	}

	/**
	 * Get the request URI for the current request.
	 *
	 * @return string
	 */
	public function get()
	{
		if (is_null($this->uri))
		{
			$uri = parse_url($this->server['REQUEST_URI'], PHP_URL_PATH);

			$this->uri = $this->format($this->clean($uri));
		}
		
		return $this->uri;
	}

	/**
	 * Remove extraneous information from the given request URI.
	 *
	 * @param  string  $uri
	 * @return string
	 */
	protected function clean($uri)
	{
		// The base application URL is removed first. If the application is being
		// served out of a sub-directory of the web document root, we need to get
		// rid of the folders that are included in the URI.
		$uri = $this->remove($uri, parse_url(Config::$items['application']['url'], PHP_URL_PATH));

		// Next, the application index file is removed. The index file has nothing
		// to do with how the request is routed to a closure or controller, so it
		// can be safely removed from the URI.
		if (($index = '/'.Config::$items['application']['index']) !== '/')
		{
			$uri = $this->remove($uri, $index);
		}

		// We don't consider the request format to be a part of the request URI.
		// The format merely determines in which format the requested resource
		// should be returned to the client.
		return rtrim($uri, '.'.Request::format($uri));
	}

	/**
	 * Remove a string from the beginning of a URI.
	 *
	 * @param  string   $uri
	 * @param  string   $remove
	 * @return string
	 */
	protected function remove($uri, $remove)
	{
		return (strpos($uri, $remove) === 0) ? substr($uri, strlen($remove)) : $uri;
	}

	/**
	 * Format the URI for use throughout the framework.
	 *
	 * If the request is to the root of the application, a single forward slash
	 * will be returned. Otherwise, the URI will be returned with all leading
	 * and trailing slashes removed.
	 *
	 * @param  string  $uri
	 * @return string
	 */
	protected function format($uri)
	{
		return (($uri = trim($uri, '/')) !== '') ? $uri : '/';
	}

}