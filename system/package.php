<?php namespace System;

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
	 * @param  string|array  $package
	 * @return void
	 */
	public static function load($package)
	{
		if (is_array($package))
		{
			foreach ($package as $value)
			{
				static::load($value);			
			}
		}

		// Packages may have a bootstrap file, which commonly is used to register auto-loaders
		// and perform other initialization needed to use the package. If the package has a
		// bootstrapper, we will require it here.
		if ( ! array_key_exists($package, static::$loaded) and file_exists($path = PACKAGE_PATH.$package.'/bootstrap'.EXT))
		{
			require $path;
		}

		static::$loaded[] = $package;
	}

}