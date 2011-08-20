<?php namespace Laravel;

class Module {

	/**
	 * The active modules for the installation.
	 *
	 * This property is set in the Laravel bootstrap file, and the modules are defined
	 * by the developer in the front controller.
	 *
	 * @var array
	 */
	public static $modules = array();

	/**
	 * All of the loaded module paths keyed by name.
	 *
	 * These are stored as the module paths are determined for convenient, fast access.
	 *
	 * @var array
	 */
	private static $paths = array();

	/**
	 * Parse a modularized identifier and return the module and key.
	 *
	 * Modular identifiers follow typically follow a {module}::{key} convention.
	 * However, for convenience, the default module does not require a module qualifier.
	 *
	 * <code>
	 *		// Returns array('admin', 'test.example')
	 *		Module::parse('admin::test.example');
	 *
	 *		// Returns array('application', 'test.example')
	 *		Module::parse('test.example');
	 * </code>
	 *
	 * @param  string  $key
	 * @return array
	 */
	public static function parse($key)
	{
		$module = (strpos($key, '::') !== false) ? substr($key, 0, strpos($key, ':')) : DEFAULT_MODULE;

		$module = str_replace('.', '/', $module);

		if ($module !== DEFAULT_MODULE) $key = substr($key, strpos($key, ':') + 2);

		return array($module, $key);
	}

	/**
	 * Get the path for a given module.
	 *
	 * Once the path has been determined, it will be cached by the class for quick access.
	 *
	 * @param  string  $module
	 * @return string
	 */
	public static function path($module)
	{
		if (array_key_exists($module, static::$paths)) return static::$paths[$module];

		if (in_array($module, static::$modules))
		{
			return static::$paths[$module] = MODULE_PATH.$module.'/';
		}
	}

	/**
	 * Get the an array of paths to all of the modules.
	 *
	 * @return array
	 */
	public static function paths()
	{
		return array_map(function($module) { return Laravel\Module::path($module); }, static::$modules);
	}

}