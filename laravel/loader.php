<?php namespace Laravel;

class Loader {

	/**
	 * The paths to be searched by the loader.
	 *
	 * @var array
	 */
	public $paths;

	/**
	 * All of the class aliases.
	 *
	 * @var array
	 */
	public $aliases;

	/**
	 * Bootstrap the auto-loader.
	 *
	 * @return void
	 */
	public function __construct($aliases, $paths)
	{
		$this->paths = $paths;
		$this->aliases = $aliases;
	}

	/**
	 * Load a class file for a given class name.
	 *
	 * This function is registered on the SPL auto-loader stack by the front controller during each request.
	 * All Laravel class names follow a namespace to directory convention.
	 *
	 * @param  string  $class
	 * @return void
	 */
	public function load($class)
	{
		$file = strtolower(str_replace('\\', '/', $class));

		if (array_key_exists($class, $this->aliases))
		{
			return class_alias($this->aliases[$class], $class);
		}

		foreach ($this->paths as $directory)
		{
			if (file_exists($path = $directory.$file.EXT))
			{
				require_once $path;

				return;
			}
		}
	}

	/**
	 * Register a path with the auto-loader.
	 *
	 * After registering the path, it will be checked similarly to the models and libraries directories.
	 *
	 * @param  string  $path
	 * @return void
	 */
	public function register_path($path)
	{
		$this->paths[] = rtrim($path, '/').'/';
	}

	/**
	 * Register an alias with the auto-loader.
	 *
	 * @param  array  $alias
	 * @return void
	 */
	public function register_alias($alias)
	{
		$this->aliases = array_merge($this->aliases, $alias);
	}

	/**
	 * Remove an alias from the auto-loader's list of aliases.
	 *
	 * @param  string  $alias
	 * @return void
	 */
	public function forget_alias($alias)
	{
		unset($this->aliases[$alias]);
	}

}