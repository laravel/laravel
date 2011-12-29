<?php namespace Laravel; defined('APP_PATH') or die('No direct script access.');

class Autoloader {

	/**
	 * The mappings from class names to file paths.
	 *
	 * @var array
	 */
	public static $mappings = array();

	/**
	 * All of the class aliases registered with the auto-loader.
	 *
	 * @var array
	 */
	public static $aliases = array();

	/**
	 * Load the file corresponding to a given class.
	 *
	 * This method is registerd in the core bootstrap file as an SPL auto-loader.
	 *
	 * @param  string  $class
	 * @return void
	 */
	public static function load($class)
	{
		// First, we'll check to see if the class has been aliased. If it has,
		// we will register the alias, which may cause the auto-loader to be
		// called again for the "real" class name.
		if (isset(static::$aliases[$class]))
		{
			class_alias(static::$aliases[$class], $class);
		}

		// All classes in Laravel are staticly mapped. There is no crazy search
		// routine that digs through directories. It's just a simple array of
		// class to file path maps for ultra-fast file loading.
		elseif (isset(static::$mappings[$class]))
		{
			require static::$mappings[$class];
		}

		// If the class is namespaced to an existing bundle and the bundle has
		// not been started, we will start the bundle and attempt to load the
		// class file again. If that fails, an error will be thrown by PHP.
		elseif (($slash = strpos($class, '\\')) !== false)
		{
			$bunde = substr($class, 0, $slash);

			if (Bundle::exists($bundle) and ! Bundle::started($bundle))
			{
				Bundle::start($bundle);

				static::load($class);
			}
		}
	}

	/**
	 * Register an array of class to path mappings.
	 *
	 * The mappings will be used to resolve file paths from class names when
	 * a class is lazy loaded through the Autoloader, providing a faster way
	 * of resolving file paths than the typical file_exists searching.
	 *
	 * <code>
	 *		// Register a class mapping with the Autoloader
	 *		Autoloader::map(array('User' => APP_PATH.'models/user'.EXT));
	 * </code>
	 *
	 * @param  array  $mappings
	 * @return void
	 */
	public static function map($mappings)
	{
		static::$mappings = array_merge(static::$mappings, $mappings);
	}

	/**
	 * Register a class alias with the auto-loader.
	 *
	 * Aliases are lazy-loaded so registering the alias does not load the class.
	 *
	 * @param  string  $class
	 * @param  string  $alias
	 * @return void
	 */
	public static function alias($class, $alias)
	{
		static::$aliases[$alias] = $class;
	}

}