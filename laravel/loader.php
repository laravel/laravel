<?php namespace Laravel;

class Loader {

	/**
	 * The paths to be searched by the loader.
	 *
	 * @var array
	 */
	public static $paths = array(BASE_PATH);

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
	 * @param  array  $paths
	 * @return void
	 */
	public static function bootstrap($paths = array())
	{
		static::$aliases = Config::get('aliases');

		foreach ($paths as $path) { static::register_path($path); }
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

		if (array_key_exists($class, static::$aliases)) return class_alias(static::$aliases[$class], $class);

		(static::load_from_registered($file)) or static::load_from_module($file);
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
	 * Search the active modules for a given file.
	 *
	 * @param  string  $file
	 * @return void
	 */
	private static function load_from_module($file)
	{
		if (is_null($module = static::module_path($file))) return;

		// Slice the module name off of the filename. Even though module libraries
		// and models are namespaced under the module, there will obviously not be
		// a folder matching that namespace in the libraries or models directory.
		$file = substr($file, strlen($module));

		foreach (array(MODULE_PATH.$module.'/models', MODULE_PATH.$module.'/libraries') as $directory)
		{
			if (file_exists($path = $directory.'/'.$file.EXT)) return require $path;
		}
	}

	/**
	 * Search the module paths for a match on the file.
	 *
	 * The file namespace should correspond to a directory within the module directory.
	 *
	 * @param  string  $file
	 * @return string
	 */
	private static function module_path($file)
	{
		foreach (Module::$modules as $key => $module)
		{
			if (strpos($file, $module) === 0) return $module;
		}
	}

	/**
	 * Register a path with the auto-loader.
	 *
	 * After registering the path, it will be checked similarly to the models and libraries directories.
	 *
	 * @param  string  $path
	 * @return void
	 */
	public static function register_path($path)
	{
		static::$paths[] = rtrim($path, '/').'/';
	}

	/**
	 * Register an alias with the auto-loader.
	 *
	 * @param  array  $alias
	 * @return void
	 */
	public static function register_alias($alias)
	{
		static::$aliases = array_merge(static::$aliases, $alias);
	}

	/**
	 * Remove an alias from the auto-loader's list of aliases.
	 *
	 * @param  string  $alias
	 * @return void
	 */
	public static function forget_alias($alias)
	{
		unset(static::$aliases[$alias]);
	}

}