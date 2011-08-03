<?php namespace System;

class Loader {

	/**
	 * The paths to be searched by the loader.
	 *
	 * @var array
	 */
	private static $paths = array(BASE_PATH, MODEL_PATH, LIBRARY_PATH);

	/**
	 * All of the class aliases.
	 *
	 * @var array
	 */
	private static $aliases = array();

	/**
	 * Bootstrap the auto-loader.
	 *
	 * @return void
	 */
	public static function bootstrap()
	{
		static::$aliases = require CONFIG_PATH.'aliases'.EXT;
	}

	/**
	 * Load a class file for a given class name.
	 *
	 * This function is registered on the SPL auto-loader stack by the front controller during each request.
	 *
	 * All Laravel class names follow a namespace to directory convention. So, if a class exists in
	 * application/libraries/user, it shouold be placed in the "User" namespace.
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