<?php namespace Laravel;

class Package {

	/**
	 * All of the loaded packages.
	 *
	 * @var array
	 */
	public static $loaded = array();

	/**
	 * Load a package or set of packages.
	 *
	 * @param  string|array  $packages
	 * @return void
	 */
	public static function load($packages)
	{
		foreach ((array) $packages as $package)
		{
			if ( ! static::loaded($package) and file_exists($bootstrap = PACKAGE_PATH.$package.'/bootstrap'.EXT))
			{
				require $bootstrap;
			}

			static::$loaded[] = $package;
		}
	}

	/**
	 * Determine if a given package has been loaded.
	 *
	 * @param  string  $package
	 * @return bool
	 */
	public static function loaded($package)
	{
		return array_key_exists($package, static::$loaded);
	}

}