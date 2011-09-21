<?php namespace Laravel;

class Loader {

	/**
	 * The paths that will be searched by the loader.
	 *
	 * @var array
	 */
	protected $paths;

	/**
	 * The class aliases defined for the application.
	 *
	 * @var array
	 */
	protected $aliases;

	/**
	 * Create a new class loader instance.
	 *
	 * @param  array  $paths
	 * @param  array  $aliases
	 * @return void
	 */
	public function __construct($paths, $aliases)
	{
		$this->paths = $paths;
		$this->aliases = $aliases;
	}

	/**
	 * Load the file for a given class.
	 *
	 * @param  string  $class
	 * @return void
	 */
	public function load($class)
	{
		// All Laravel core classes follow a namespace to directory convention. So, we will
		// replace all of the namespace slashes with directory slashes.
		$file = strtolower(str_replace('\\', '/', $class));

		// First, we'll check to determine if an alias exists. If it does, we will define the
		// alias and bail out. Aliases are defined for most developer used core classes.
		if (array_key_exists($class, $this->aliases)) return class_alias($this->aliases[$class], $class);

		foreach ($this->paths as $path)
		{
			if (file_exists($path = $path.$file.EXT))
			{
				require_once $path;

				return;
			}
		}
	}

	/**
	 * Register a class alias with the auto-loader.
	 *
	 * Note: Aliases are lazy-loaded, so the aliased class will not be included until it is needed.
	 *
	 * <code>
	 *		// Register an alias for the "SwiftMailer\Transport" class
	 *		Loader::alias('Transport', 'SwiftMailer\\Transport');
	 * </code>
	 *
	 * @param  string  $alias
	 * @param  string  $class
	 * @return void
	 */
	public function alias($alias, $class)
	{
		$this->aliases[$alias] = $class;
	}

	/**
	 * Register a path with the auto-loader.
	 *
	 * The registered path will be searched when auto-loading classes.
	 *
	 * <code>
	 *		// Register a path to be searched by the auto-loader
	 *		Loader::path('path/to/files');
	 * </code>
	 *
	 * @param  string  $path
	 * @return void
	 */
	public function path($path)
	{
		$this->paths[] = rtrim($path, '/').'/';
	}

	/**
	 * Remove an alias from the auto-loader's alias registrations.
	 *
	 * <code>
	 *		// Remove the "Transport" alias from the registered aliases
	 *		Loader::forget_alias('Transport');
	 * </code>
	 *
	 * @param  string  $alias
	 * @return void
	 */
	public function forget_alias($alias)
	{
		unset($this->aliases[$alias]);
	}

}