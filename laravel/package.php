<?php namespace Laravel;

class Package extends Facade { public static $resolve = 'package'; }

class Package_Engine {

	/**
	 * All of the loaded packages.
	 *
	 * @var array
	 */
	public $loaded = array();

	/**
	 * Load a package or set of packages.
	 *
	 * The package name should correspond to a package directory for your application.
	 *
	 * @param  string|array  $packages
	 * @param  string        $path
	 * @return void
	 */
	public function load($packages, $path)
	{
		foreach ((array) $packages as $package)
		{
			if ( ! $this->loaded($package) and file_exists($bootstrap = $path.$package.'/bootstrap'.EXT))
			{
				require $bootstrap;
			}

			$this->loaded[] = $package;
		}
	}

	/**
	 * Determine if a given package has been loaded.
	 *
	 * @param  string  $package
	 * @return bool
	 */
	public function loaded($package)
	{
		return array_key_exists($package, $this->loaded);
	}

}