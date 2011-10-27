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
	 * If the request is to the root of the application, a single forward slash
	 * will be returned. Otherwise, the URI will be returned with all leading
	 * and trailing slashes removed. The application URL and index file will
	 * also be removed since they are not used when routing the request.
	 *
	 * @return string
	 */
	public function get()
	{
		if ( ! is_null($this->uri)) return $this->uri;

		return $this->uri = $this->format($this->clean($this->parse($this->server['REQUEST_URI'])));
	}

	/**
	 * Remove extraneous information from the given request URI.
	 *
	 * @param  string  $uri
	 * @return string
	 */
	protected function clean($uri)
	{
		$uri = $this->remove($uri, $this->parse(Config::$items['application']['url']));

		if (($index = '/'.Config::$items['application']['index']) !== '/')
		{
			$uri = $this->remove($uri, $index);
		}

		return $uri;
	}

	/**
	 * Parse a given string URI using PHP_URL_PATH to remove the domain.
	 *
	 * @return string
	 */
	protected function parse($uri)
	{
		return parse_url($uri, PHP_URL_PATH);
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
	 * @param  string  $uri
	 * @return string
	 */
	protected function format($uri)
	{
		return (($uri = trim($uri, '/')) !== '') ? $uri : '/';
	}

}