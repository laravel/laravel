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
		// After PHP namespaces were introduced, most libaries ditched underscores for
		// for namespaces to indicate the class directory hierarchy. We will check for
		// the present of namespace slashes to determine the directory separator.
		$separator = (strpos($class, '\\') !== false) ? '\\' : '_';

		$library = substr($class, 0, strpos($class, $separator));

		$file = str_replace('\\', '/', $class);

		// If the namespace has been registered as a PSR-0 compliant library, we will
		// load the library according to the PSR-0 naming standards, which state that
		// namespaces and underscores indicate the directory hierarchy of the class.
		//
		// The PSR-0 standard is exactly like the typical Laravel standard, the only
		// difference being that Laravel files are all lowercase, while PSR-0 states
		// that the file name should match the class name.
		if (isset(static::$libraries[$library]))
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

		// If we could not find the class file in any of the auto-loaded locations
		// according to the Laravel naming standard, we will search the libraries
		// directory for the class according to the PSR-0 naming standard.
		if (file_exists($path = LIBRARY_PATH.str_replace('_', '/', $file).EXT))
		{
			static::$libraries[] = $library;

			return $path;
		}

		// Since not all controllers will be resolved by the controller resolver,
		// we will do a quick check in the controller directory for the class.
		// For instance, since base controllers would not be resolved by the
		// controller class, we will need to resolve them here.
		if (strpos($class, '_Controller') !== false)
		{
			$controller = str_replace(array('_Controller', '_'), array('', '/'), $class);

			if (file_exists($path = strtolower(CONTROLLER_PATH.$controller.EXT)))
			{
				return $path;
			}
		}
	}

}