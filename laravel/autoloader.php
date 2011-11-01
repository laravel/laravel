<?php namespace Laravel;

class Autoloader {

	/**
	 * The class alises defined for the application.
	 *
	 * @var array
	 */
	protected $aliases = array();

	/**
	 * The PSR-0 compliant libraries registered with the auto-loader.
	 *
	 * @var array
	 */
	protected $libraries = array();

	/**
	 * The paths to be searched by the auto-loader.
	 *
	 * @var array
	 */
	protected $paths = array(BASE_PATH, CLASS_PATH);

	/**
	 * Create a new auto-loader instance.
	 *
	 * @param  array  $aliases
	 * @return void
	 */
	public function __construct($aliases = array())
	{
		$this->aliases = $aliases;
	}

	/**
	 * Load the file corresponding to a given class.
	 *
	 * @param  string  $class
	 * @return void
	 */
	public function load($class)
	{
		// Most of the core classes are aliases for convenient access in spite
		// of the namespace. If an alias is defined for the class, we will load
		// the alias and bail out of the auto-load method.
		if (array_key_exists($class, $this->aliases))
		{
			return class_alias($this->aliases[$class], $class);
		}

		$file = str_replace('\\', '/', $class);

		$namespace = substr($class, 0, strpos($class, '\\'));

		// If the class namespace exists in the libraries array, it means that
		// the library is PSR-0 compliant, and we will load it following those
		// standards. This allows us to add many third-party libraries to an
		// application and be able to auto-load them automatically.
		if (array_key_exists($namespace, $this->libraries))
		{
			require CLASS_PATH.$this->psr($file);
		}

		foreach ($this->paths as $path)
		{
			if (file_exists($path = $path.strtolower($file).EXT))
			{
				require $path;

				return;
			}
		}

		// If the namespace exists in the classes directory, we will assume the
		// library is PSR-0 compliant, and will add the namespace to the array
		// of libraries and load the class accordingly.
		if (is_dir(CLASS_PATH.$namespace))
		{
			$this->libraries[] = $namespace;

			require CLASS_PATH.$this->psr($file);
		}
	}

	/**
	 * Format a path for PSR-0 compliant auto-loading.
	 *
	 * @param  string  $file
	 * @return string
	 */
	protected function psr($file)
	{
		return str_replace('_', '/', $file);
	}

}