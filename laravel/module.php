<?php namespace Laravel;

class Module {

	/**
	 * The active modules for the installation.
	 *
	 * @var array
	 */
	public static $modules = array();

	/**
	 * All of the loaded module paths.
	 *
	 * @var array
	 */
	private static $paths = array();

	/**
	 * Parse a modularized identifier and get the module and key.
	 *
	 * Modular identifiers follow a {module}::{key} convention.
	 *
	 * @param  string  $key
	 * @return array
	 */
	public static function parse($key)
	{
		$module = (strpos($key, '::') !== false) ? substr($key, 0, strpos($key, ':')) : DEFAULT_MODULE;

		if ($module !== DEFAULT_MODULE) $key = substr($key, strpos($key, ':') + 2);

		return array($module, $key);
	}

	/**
	 * Get the path for a given module.
	 *
	 * @param  string  $module
	 * @return string
	 */
	public static function path($module)
	{
		if (array_key_exists($module, static::$paths)) return static::$paths[$module];

		if (array_key_exists($module, static::$modules))
		{
			$path = (strpos(static::$modules[$module], BASE_PATH) === 0) ? static::$modules[$module].'/' : MODULE_PATH.static::$modules[$module].'/';
		}

		return static::$paths[$module] = $path;
	}

	/**
	 * Get the an array of paths to all of the modules.
	 *
	 * The module paths will be determined by the modules that are specified in the application
	 * modules configuration item. A trailing slash will be added to the paths.
	 *
	 * @return array
	 */
	public static function paths()
	{
		return array_map(function($module) { return Laravel\Module::path($module); }, static::$modules);
	}

}