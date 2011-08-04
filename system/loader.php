<?php namespace System;

class Loader {

	/**
	 * The paths to be searched by the loader.
	 *
	 * @var array
	 */
	public static $paths = array(BASE_PATH, MODEL_PATH, LIBRARY_PATH);

	/**
	 * All of the class aliases.
	 *
	 * @var array
	 */
	public static $aliases = array();

	/**
	 * All of the active modules.
	 *
	 * @var array
	 */
	public static $modules = array();

	/**
	 * Bootstrap the auto-loader.
	 *
	 * @return void
	 */
	public static function bootstrap()
	{
		static::$aliases = Config::get('aliases');
		static::$modules = Config::get('application.modules');
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
	public static function load($class)
	{
		$file = strtolower(str_replace('\\', '/', $class));

		if (array_key_exists($class, static::$aliases))
		{
			return class_alias(static::$aliases[$class], $class);
		}

		if ( ! static::load_from_registered($file))
		{
			static::load_from_module($file);
		}
	}

	/**
	 * Load a class that is stored in the registered directories.
	 *
	 * @param  string  $file
	 * @return bool
	 */
	private static function load_from_registered($file)
	{
		foreach (static::$paths as $directory)
		{
			if (file_exists($path = $directory.$file.EXT))
			{
				require $path;

				return true;
			}
		}

		return false;
	}

	/**
	 * Load a class that is stored in a module.
	 *
	 * @param  string  $file
	 * @return void
	 */
	private static function load_from_module($file)
	{
		// Since all module models and libraries must be namespaced to the
		// module name, we'll extract the module name from the file.
		$module = substr($file, 0, strpos($file, '/'));

		if (in_array($module, static::$modules))
		{
			$module = MODULE_PATH.$module.'/';

			// Slice the module name off of the filename. Even though module libraries
			// and models are namespaced under the module, there will obviously not be
			// a folder matching that namespace in the libraries or models directories
			// of the module. Slicing it off will allow us to make a clean search for
			// the relevant class file.
			$file = substr($file, strpos($file, '/') + 1);

			foreach (array($module.'models', $module.'libraries') as $directory)
			{
				if (file_exists($path = $directory.'/'.$file.EXT))
				{
					return require $path;
				}
			}
		}
	}

	/**
	 * Register a path with the auto-loader. After registering the path, it will be
	 * checked similarly to the models and libraries directories.
	 *
	 * @param  string  $path
	 * @return void
	 */
	public static function register($path)
	{
		static::$paths[] = rtrim($path, '/').'/';
	}

}