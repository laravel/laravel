<?php namespace Laravel\Routing;

use Laravel\Arr;

class Loader {

	/**
	 * The location of the base routes file.
	 *
	 * @var string
	 */
	protected $base;

	/**
	 * The directory containing nested route files.
	 *
	 * @var string
	 */
	protected $nest;

	/**
	 * A cache for all of the routes defined for the entire application.
	 *
	 * @var array
	 */
	protected $everything;

	/**
	 * Create a new route loader instance.
	 *
	 * @param  string  $base
	 * @param  string  $nest
	 * @return void
	 */
	public function __construct($base, $nest)
	{
		$this->base = $base;
		$this->nest = $nest;
	}

	/**
	 * Load the applicable routes for a given URI.
	 *
	 * @param  string  $uri
	 * @return array
	 */
	public function load($uri)
	{
		$routes = (file_exists($path = $this->base.'routes'.EXT)) ? require $path : array();

		return array_merge($this->nested(Arr::without(explode('/', $uri), array(''))), $routes);
	}

	/**
	 * Get the appropriate routes from the routes directory for a given URI.
	 *
	 * @param  array  $segments
	 * @return array
	 */
	protected function nested($segments)
	{
		// Work backwards through the URI segments until we find the deepest possible
		// matching route directory. Once we find it, we will return those routes.
		foreach (array_reverse($segments, true) as $key => $value)
		{
			if (file_exists($path = $this->nest.implode('/', array_slice($segments, 0, $key + 1)).EXT))
			{
				return require $path;
			}
			elseif (file_exists($path = str_replace('.php', '/routes.php', $path)))
			{
				return require $path;
			}
		}

		return array();
	}

	/**
	 * Get every route defined for the application.
	 *
	 * @return array
	 */
	public function everything()
	{
		if ( ! is_null($this->everything)) return $this->everything;

		$routes = array();

		// Since route files can be nested deep within the route directory, we need to
		// recursively spin through the directory to find every file.
		$directoryIterator = new \RecursiveDirectoryIterator($this->nest);

		$recursiveIterator = new \RecursiveIteratorIterator($directoryIterator, \RecursiveIteratorIterator::SELF_FIRST);

		foreach ($recursiveIterator as $file)
		{
			if (filetype($file) === 'file')
			{
				$routes = array_merge(require $file, $routes);
			}
		}

		return $this->everything = $routes;
	}

}