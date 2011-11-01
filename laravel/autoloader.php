<?php namespace Laravel;

class Autoloader {

	/**
	 * The PSR-0 compliant libraries registered with the auto-loader.
	 *
	 * @var array
	 */
	protected static $libraries = array();

	/**
	 * The paths to be searched by the auto-loader.
	 *
	 * @var array
	 */
	protected static $paths = array(BASE_PATH, MODEL_PATH, LIBRARY_PATH);

	/**
	 * Load the file corresponding to a given class.
	 *
	 * @param  string  $class
	 * @return void
	 */
	public static function load($class)
	{
		// Most of the core classes are aliases for convenient access in spite of
		// the namespace. If an alias is defined for the class, we will load the
		// alias and bail out of the auto-load method.
		if (array_key_exists($class, Config::$items['application']['aliases']))
		{
			return class_alias(Config::$items['application']['aliases'][$class], $class);
		}

		if ( ! is_null($path = static::find($class)))
		{
			require $path;
		}
	}

	/**
	 * Find the file associated with a given class name.
	 *
	 * @param  string  $class
	 * @return string
	 */
	protected static function find($class)
	{
		$file = str_replace('\\', '/', $class);

		$namespace = substr($class, 0, strpos($class, '\\'));

		// If the class namespace exists in the libraries array, it means that the
		// library is PSR-0 compliant, and we will load it following those standards.
		// This allows us to add many third-party libraries to an application and be
		// able to auto-load them automatically.
		if (array_key_exists($namespace, static::$libraries))
		{
			return LIBRARY_PATH.str_replace('_', '/', $file);
		}

		foreach (static::$paths as $path)
		{
			if (file_exists($path = $path.strtolower($file).EXT))
			{
				return $path;
			}
		}

		// If the file exists as-is in the libraries directory, we will assume the
		// library is PSR-0 compliant, and will add the namespace to the array of
		// libraries and load the class accordingly.
		if (file_exists($path = LIBRARY_PATH.str_replace('_', '/', $file)))
		{
			static::$libraries[] = $namespace;

			return $path;
		}
	}

}