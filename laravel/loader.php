<?php namespace Laravel;

class Loader {

	/**
	 * The paths that will be searched by the loader.
	 *
	 * @var array
	 */
	public $paths;

	/**
	 * The class aliases defined for the application.
	 *
	 * @var array
	 */
	public $aliases;

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
		$file = strtolower(str_replace('\\', '/', $class));

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

}