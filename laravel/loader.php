<?php namespace Laravel;

class Loader {

	/**
	 * The paths that will be searched by the loader.
	 *
	 * @var array
	 */
	protected $paths = array();

	/**
	 * The class aliases defined for the application.
	 *
	 * @var array
	 */
	protected $aliases = array();

	/**
	 * Create a new class loader instance.
	 *
	 * @param  array  $paths
	 * @param  array  $aliases
	 * @return void
	 */
	public function __construct($paths, $aliases = array())
	{
		$this->paths = $paths;
		$this->aliases = $aliases;
	}

	/**
	 * Load the file for a given class.
	 *
	 * <code>
	 *		// Load the file for the "User" class
	 *		Loader::load('User');
	 *
	 *		// Load the file for the "Repositories\User" class
	 *		Loader::load('Repositories\\User');
	 * </code>
	 *
	 * @param  string  $class
	 * @return void
	 */
	public function load($class)
	{
		// All Laravel core classes follow a namespace to directory convention.
		// We will replace all of the namespace slashes with directory slashes.
		$file = strtolower(str_replace('\\', '/', $class));

		// Check to determine if an alias exists. If it does, we will define the
		// alias and bail out. Aliases are defined for most used core classes.
		if (array_key_exists($class, $this->aliases))
		{
			return class_alias($this->aliases[$class], $class);
		}

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
	 * @param  string  $alias
	 * @return void
	 */
	public function forget_alias($alias)
	{
		unset($this->aliases[$alias]);
	}

}