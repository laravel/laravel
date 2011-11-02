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
	 * Laravel loads classes out of three directories: the core "laravel" directory,
	 * and the application "models" and "libraries" directories. All of the file
	 * names are lower cased and the directory structure corresponds with the
	 * class namespaces.
	 *
	 * The application "libraries" directory also supports the inclusion of PSR-0
	 * compliant libraries. These libraries will be detected automatically and
	 * will be loaded according to the PSR-0 naming conventions.
	 *
	 * @param  string  $class
	 * @return void
	 */
	public static function load($class)
	{
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
	 * Determine the file path associated with a given class name.
	 *
	 * @param  string  $class
	 * @return string
	 */
	protected static function find($class)
	{
		$file = str_replace('\\', '/', $class);

		$namespace = substr($class, 0, strpos($class, '\\'));

		// If the namespace has been detected as a PSR-0 compliant library,
		// we will load the library according to those naming conventions.
		if (array_key_exists($namespace, static::$libraries))
		{
			return str_replace('_', '/', $file).EXT;
		}

		foreach (static::$paths as $path)
		{
			if (file_exists($path = $path.strtolower($file).EXT))
			{
				return $path;
			}
		}

		// If the file exists according to the PSR-0 naming conventions,
		// we will add the namespace to the array of libraries and load
		// the class according to the PSR-0 conventions.
		if (file_exists($path = str_replace('_', '/', $file).EXT))
		{
			static::$libraries[] = $namespace;

			return $path;
		}
	}

}