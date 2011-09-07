<?php namespace Laravel;

class URL {

	/**
	 * Create a new URL writer instance.
	 *
	 * @param  Router  $router
	 * @param  string  $base
	 * @param  string  $index
	 * @param  bool    $https
	 * @return void
	 */
	public function __construct(Routing\Router $router, $base, $index, $https)
	{
		$this->base = $base;
		$this->https = $https;
		$this->index = $index;
		$this->router = $router;
	}

	/**
	 * Generate an application URL.
	 *
	 * If the given URL is already well-formed, it will be returned unchanged.
	 *
	 * @param  string  $url
	 * @param  bool    $https
	 * @return string
	 */
	public function to($url = '', $https = false)
	{
		if (filter_var($url, FILTER_VALIDATE_URL) !== false) return $url;

		$base = $this->base.'/'.$this->index;

		if ($https) $base = preg_replace('~http://~', 'https://', $base, 1);

		return rtrim($base, '/').'/'.trim($url, '/');
	}

	/**
	 * Generate an application URL with HTTPS.
	 *
	 * @param  string  $url
	 * @return string
	 */
	public function to_secure($url = '')
	{
		return $this->to($url, true);
	}

	/**
	 * Generate an application URL to an asset.
	 *
	 * The index file will not be added to asset URLs. If the HTTPS option is not
	 * specified, HTTPS will be used when the active request is also using HTTPS.
	 *
	 * @param  string  $url
	 * @param  bool    $https
	 * @return string
	 */
	public function to_asset($url, $https = null)
	{
		if (is_null($https)) $https = $this->https;

		return str_replace('index.php/', '', $this->to($url, $https));
	}

	/**
	 * Generate a URL from a route name.
	 *
	 * For routes that have wildcard parameters, an array may be passed as the second
	 * parameter to the method. The values of this array will be used to fill the
	 * wildcard segments of the route URI.
	 *
	 * Optional parameters will be convereted to spaces if no parameter values are specified.
	 *
	 * @param  string          $name
	 * @param  array           $parameters
	 * @param  bool            $https
	 * @return string
	 */
	public function to_route($name, $parameters = array(), $https = false)
	{
		if ( ! is_null($route = $this->router->find($name)))
		{
			$uris = explode(', ', key($route));

			$uri = substr($uris[0], strpos($uris[0], '/'));

			foreach ($parameters as $parameter)
			{
				$uri = preg_replace('/\(.+?\)/', $parameter, $uri, 1);
			}

			return $this->to(str_replace(array('/(:any?)', '/(:num?)'), '', $uri), $https);
		}

		throw new \Exception("Error generating named route for route [$name]. Route is not defined.");
	}

	/**
	 * Generate a HTTPS URL from a route name.
	 *
	 * @param  string  $name
	 * @param  array   $parameters
	 * @return string
	 */
	public function to_secure_route($name, $parameters = array())
	{
		return $this->to_route($name, $parameters, true);
	}

	/**
	 * Generate a URL friendly "slug".
	 *
	 * @param  string  $title
	 * @param  string  $separator
	 * @return string
	 */
	public function slug($title, $separator = '-')
	{
		$title = Str::ascii($title);

		// Remove all characters that are not the separator, letters, numbers, or whitespace.
		$title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', Str::lower($title));

		// Replace all separator characters and whitespace by a single separator
		$title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

		return trim($title, $separator);
	}

	/**
	 * Magic Method for dynamically creating URLs to named routes.
	 */
	public function __call($method, $parameters)
	{
		$parameters = (isset($parameters[0])) ? $parameters[0] : array();

		if (strpos($method, 'to_secure_') === 0)
		{
			return $this->to_route(substr($method, 10), $parameters, true);
		}

		if (strpos($method, 'to_') === 0)
		{
			return $this->to_route(substr($method, 3), $parameters);
		}

		throw new \Exception("Method [$method] is not defined on the URL class.");
	}

}