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
	 * Bootstrap the auto-loader.
	 *
	 * @param  array  $aliases
	 * @param  array  $paths
	 * @return void
	 */
	public static function bootstrap($aliases, $paths)
	{
		static::$aliases = $aliases;

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

		if (array_key_exists($class, static::$aliases))
		{
			return class_alias(static::$aliases[$class], $class);
		}

		foreach (static::$paths as $directory)
		{
			if (file_exists($path = $directory.$file.EXT))
			{
				require $path;

				return;
			}
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