<?php namespace Laravel\Routing;

use Laravel\Arr;
use RecursiveIteratorIterator as Iterator;
use RecursiveDirectoryIterator as DirectoryIterator;

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
	 * The application route directory will be checked for nested route files and an
	 * array of all applicable routes will be returned based on the URI segments.
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

			// Even if we didn't find a matching file for the segment, we still want to
			// check for a "routes.php" file which could handle the root route and any
			// routes that are impossible to handle in an explicitly named file.
			if (file_exists($path = str_replace('.php', '/routes.php', $path)))
			{
				return require $path;
			}
		}

		return array();
	}

	/**
	 * Get every route defined for the application.
	 *
	 * For fast performance, if the routes have already been loaded once, they will not
	 * be loaded again, and the same routes will be returned on subsequent calls.
	 *
	 * @return array
	 */
	public function everything()
	{
		if ( ! is_null($this->everything)) return $this->everything;

		$routes = array();

		// First we will check for the base routes file in the application directory.
		if (file_exists($path = $this->base.'routes'.EXT))
		{
			$routes = array_merge($routes, require $path);
		}

		// Since route files can be nested deep within the route directory, we need to
		// recursively spin through each directory to find every file.
		$iterator = new Iterator(new DirectoryIterator($this->nest), Iterator::SELF_FIRST);

		foreach ($iterator as $file)
		{
			// Since some Laravel developers may place HTML files in the route directories, we will
			// check for the PHP extension before merging the file. Typically, the HTML files are
			// present in installations that are not using mod_rewrite and the public directory.
			if (filetype($file) === 'file' and strpos($file, EXT) !== false)
			{
				$routes = array_merge(require $file, $routes);
			}
		}

		return $this->everything = $routes;
	}

}