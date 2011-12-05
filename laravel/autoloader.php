<?php namespace Laravel;

class Autoloader {

	/**
	 * The mappings from class names to file paths.
	 *
	 * @var array
	 */
	public static $mappings = array();

	/**
	 * The PSR-0 compliant libraries registered with the loader.
	 *
	 * @var array
	 */
	public static $libraries = array();

	/**
	 * The paths to be searched by the loader.
	 *
	 * @var array
	 */
	protected static $paths = array(MODEL_PATH, LIBRARY_PATH);

	/**
	 * Load the file corresponding to a given class.
	 *
	 * This method is registerd in the core bootstrap file as an SPL Autoloader.
	 *
	 * @param  string  $class
	 * @return void
	 */
	public static function load($class)
	{
		if (isset(Config::$items['application']['aliases'][$class]))
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
		// First we will look for the class in the hard-coded class mappings, since
		// this is the fastest way to resolve a class name to its associated file.
		// This saves us from having to search through the file system manually.
		if (isset(static::$mappings[$class]))
		{
			return static::$mappings[$class];
		}

		// If the library has been registered as a PSR-0 compliant library, we will
		// load the library according to the PSR-0 naming standards, which state that
		// namespaces and underscores indicate the directory hierarchy of the class.
		if (in_array(static::library($class), static::$libraries))
		{
			return LIBRARY_PATH.str_replace(array('\\', '_'), '/', $class).EXT;
		}

		// Next we will search through the common Laravel paths for the class file.
		// The Laravel libraries and models directories will be searched according
		// to the Laravel class naming standards.
		$file = strtolower(str_replace('\\', '/', $class));

		foreach (static::$paths as $path)
		{
			if (file_exists($path = $path.$file.EXT))
			{
				return $path;
			}
		}

		// Since not all controllers will be resolved by the controller resolver,
		// we will do a quick check in the controller directory for the class.
		// For instance, since base controllers would not be resolved by the
		// controller class, we will need to resolve them here.
		if (file_exists($path = static::controller($class)))
		{
			return $path;
		}
	}

	/**
	 * Extract the "library" name from the given class.
	 *
	 * The library name is essentially the namespace, or the string that preceeds
	 * the first PSR-0 separator. PSR-0 states that namespaces or undescores may
	 * be used to indicate the directory structure in which the file resides.
	 *
	 * @param  string  $class
	 * @return string
	 */
	protected static function library($class)
	{
		if (($separator = strpos($class, '\\')) !== false)
		{
			return substr($class, 0, $separator);
		}
		elseif (($separator = strpos($class, '_')) !== false)
		{
			return substr($class, 0, $separator);
		}
	}

	/**
	 * Translate a given controller class name into the corresponding file name.
	 *
	 * The controller suffix will be removed, and the underscores will be translated
	 * into directory slashes. Of course, the entire class name will be converted to
	 * lower case as well.
	 *
	 * <code>
	 *		// Returns "user/profile"...
	 *		$file = static::controller('User_Profile_Controller');
	 * </code>
	 *
	 * @param  string  $class
	 * @return string
	 */
	protected static function controller($class)
	{
		$controller = str_replace(array('_', '_Controller'), array('/', ''), $class);

		return CONTROLLER_PATH.strtolower($controller).EXT;
	}

	/**
	 * Register an array of class to path mappings.
	 *
	 * The mappings will be used to resolve file paths from class names when
	 * a class is lazy loaded through the Autoloader, providing a faster way
	 * of resolving file paths than the typical file_exists method.
	 *
	 * <code>
	 *		// Register a class mapping with the Autoloader
	 *		Autoloader::maps(array('User' => MODEL_PATH.'user'.EXT));
	 * </code>
	 *
	 * @param  array  $mappings
	 * @return void
	 */
	public static function maps($mappings)
	{
		foreach ($mappings as $class => $path)
		{
			static::$mappings[$class] = $path;
		}
	}

	/**
	 * Register PSR-0 libraries with the Autoloader.
	 *
	 * The library names given to this method should match directories within
	 * the application libraries directory. This method provides an easy way
	 * to indicate that some libraries should be loaded using the PSR-0
	 * naming conventions instead of the Laravel conventions.
	 *
	 * <code>
	 *		// Register the "Assetic" library with the Autoloader
	 *		Autoloader::libraries('Assetic');
	 *
	 *		// Register several libraries with the Autoloader
	 *		Autoloader::libraries(array('Assetic', 'Twig'));
	 * </code>
	 *
	 * @param  array  $libraries
	 * @return void
	 */
	public static function libraries($libraries)
	{
		static::$libraries = array_merge(static::$libraries, (array) $libraries);
	}

}