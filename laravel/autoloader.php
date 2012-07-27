<?php namespace Laravel;

class Autoloader {

	/**
	 * The mappings from class names to file paths.
	 *
	 * @var array
	 */
	public static $mappings = array();

	/**
	 * The directories that use the PSR-0 naming convention.
	 *
	 * @var array
	 */
	public static $directories = array();

	/**
	 * The mappings for namespaces to directories.
	 *
	 * @var array
	 */
	public static $namespaces = array();

	/**
	 * The mappings for underscored libraries to directories.
	 *
	 * @var array
	 */
	public static $underscored = array();

	/**
	 * All of the class aliases registered with the auto-loader.
	 *
	 * @var array
	 */
	public static $aliases = array();

	/**
	 * Load the file corresponding to a given class.
	 *
	 * This method is registered in the bootstrap file as an SPL auto-loader.
	 *
	 * @param  string  $class
	 * @return void
	 */
	public static function load($class)
	{
		// First, we will check to see if the class has been aliased. If it has,
		// we will register the alias, which may cause the auto-loader to be
		// called again for the "real" class name to load its file.
		if (isset(static::$aliases[$class]))
		{
			return class_alias(static::$aliases[$class], $class);
		}

		// All classes in Laravel are statically mapped. There is no crazy search
		// routine that digs through directories. It's just a simple array of
		// class to file path maps for ultra-fast file loading.
		elseif (isset(static::$mappings[$class]))
		{
			require static::$mappings[$class];

			return;
		}

		// If the class namespace is mapped to a directory, we will load the
		// class using the PSR-0 standards from that directory accounting
		// for the root of the namespace by trimming it off.
		foreach (static::$namespaces as $namespace => $directory)
		{
			if (starts_with($class, $namespace))
			{
				return static::load_namespaced($class, $namespace, $directory);
			}
		}

		static::load_psr($class);
	}

	/**
	 * Load a namespaced class from a given directory.
	 *
	 * @param  string  $class
	 * @param  string  $namespace
	 * @param  string  $directory
	 * @return void
	 */
	protected static function load_namespaced($class, $namespace, $directory)
	{
		return static::load_psr(substr($class, strlen($namespace)), $directory);
	}

	/**
	 * Attempt to resolve a class using the PSR-0 standard.
	 *
	 * @param  string  $class
	 * @param  string  $directory
	 * @return void
	 */
	protected static function load_psr($class, $directory = null)
	{
		// The PSR-0 standard indicates that class namespaces and underscores
		// should be used to indicate the directory tree in which the class
		// resides, so we'll convert them to slashes.
		$file = str_replace(array('\\', '_'), '/', $class);

		$directories = $directory ?: static::$directories;

		$lower = strtolower($file);

		// Once we have formatted the class name, we'll simply spin through
		// the registered PSR-0 directories and attempt to locate and load
		// the class file into the script.
		foreach ((array) $directories as $directory)
		{
			if (file_exists($path = $directory.$lower.EXT))
			{
				return require $path;
			}
			elseif (file_exists($path = $directory.$file.EXT))
			{
				return require $path;
			}
		}
	}

	/**
	 * Register an array of class to path mappings.
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
	 * @param  string  $class
	 * @param  string  $alias
	 * @return void
	 */
	public static function alias($class, $alias)
	{
		static::$aliases[$alias] = $class;
	}

	/**
	 * Register directories to be searched as a PSR-0 library.
	 *
	 * @param  string|array  $directory
	 * @return void
	 */
	public static function directories($directory)
	{
		$directories = static::format($directory);

		static::$directories = array_unique(array_merge(static::$directories, $directories));
	}

	/**
	 * Map namespaces to directories.
	 *
	 * @param  array   $mappings
	 * @param  string  $append
	 * @return void
	 */
	public static function namespaces($mappings, $append = '\\')
	{
		$mappings = static::format_mappings($mappings, $append);

		static::$namespaces = array_merge($mappings, static::$namespaces);
	}

	/**
	 * Register underscored "namespaces" to directory mappings.
	 *
	 * @param  array  $mappings
	 * @return void
	 */
	public static function underscored($mappings)
	{
		static::namespaces($mappings, '_');
	}

	/**
	 * Format an array of namespace to directory mappings.
	 *
	 * @param  array   $mappings
	 * @param  string  $append
	 * @return array
	 */
	protected static function format_mappings($mappings, $append)
	{
		foreach ($mappings as $namespace => $directory)
		{
			// When adding new namespaces to the mappings, we will unset the previously
			// mapped value if it existed. This allows previously registered spaces to
			// be mapped to new directories on the fly.
			$namespace = trim($namespace, $append).$append;

			unset(static::$namespaces[$namespace]);

			$namespaces[$namespace] = head(static::format($directory));
		}

		return $namespaces;
	}

	/**
	 * Format an array of directories with the proper trailing slashes.
	 *
	 * @param  array  $directories
	 * @return array
	 */
	protected static function format($directories)
	{
		return array_map(function($directory)
		{
			return rtrim($directory, DS).DS;
		
		}, (array) $directories);
	}

}