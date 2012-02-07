<?php namespace Laravel; defined('DS') or die('No direct script access.');

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
	public static $psr = array();

	/**
	 * The mappings for namespaces to directories.
	 *
	 * @var array
	 */
	public static $namespaces = array();

	/**
	 * All of the class aliases registered with the auto-loader.
	 *
	 * @var array
	 */
	public static $aliases = array();

	/**
	 * Load the file corresponding to a given class.
	 *
	 * This method is registerd in the bootstrap file as an SPL auto-loader.
	 *
	 * @param  string  $class
	 * @return void
	 */
	public static function load($class)
	{
		// First, we will check to see if the class has been aliased. If it has,
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

		// If the class namespace is mapped to a directory, we will load the
		// class using the PSR-0 standards from that directory; however, we
		// will trim off the beginning of the namespace to account for
		// the root of the mapped directory.
		if ( ! is_null($info = static::namespaced($class)))
		{
			$class = substr($class, strlen($info['namespace']));

			return static::load_psr($class, $info['directory']);
		}

		elseif (($slash = strpos($class, '\\')) !== false)
		{
			$namespace = substr($class, 0, $slash);

			// If the class is namespaced to an existing bundle and the bundle has
			// not been started, we will start the bundle and attempt to load the
			// class file again. If that fails, an error will be thrown by PHP.
			//
			// This allows bundle classes to be loaded by the auto-loader before
			// their class mappings have actually been registered; however, it
			// is up to the bundle developer to namespace their classes to
			// match the name of their bundle.
			if (Bundle::exists($namespace) and ! Bundle::started($namespace))
			{
				Bundle::start(strtolower($namespace));

				static::load($class);
			}
		}

		// If the class is not maped and is not part of a bundle or a mapped
		// namespace, we'll make a last ditch effort to load the class via
		// the PSR-0 from one of the registered directories.
		static::load_psr($class);
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
		// shoould be used to indcate the directory tree in which the class
		// resides, so we'll convert them to directory slashes.
		$file = str_replace(array('\\', '_'), '/', $class);

		$directories = $directory ?: static::$psr;

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
	 * Get the directory for a given namespaced class.
	 *
	 * @param  string  $class
	 * @return string
	 */
	protected static function namespaced($class)
	{
		foreach (static::$namespaces as $namespace => $directory)
		{
			if (starts_with($class, $namespace))
			{
				return compact('namespace', 'directory');
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
	public static function psr($directory)
	{
		$directories = static::format($directory);

		static::$psr = array_unique(array_merge(static::$psr, $directories));
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
	 * Map namespaces to directories.
	 *
	 * @param  array   $mappings
	 * @param  string  $append
	 * @return void
	 */
	public static function namespaces($mappings, $append = '\\')
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

		// We'll array_merge the new mappings onto the front of the array so
		// derivative namespaces are not always shadowed by their parents.
		// For instance, when mappings Laravel\Docs, we don't want the
		// main Laravel namespace to always override it.
		static::$namespaces = array_merge($namespaces, static::$namespaces);
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