<?php namespace Laravel\CLI\Tasks\Bundle\Providers;

abstract class Provider {

	/**
	 * Install the given bundle into the application.
	 *
	 * @param  string  $bundle
	 * @return void
	 */
	abstract public function install($bundle);

	/**
	 * Create the path to the bundle's dirname.
	 *
	 * @param  array  $bundle
	 * @return void
	 */
	protected function directory($bundle)
	{
		// If the installation target directory doesn't exist, we will create
		// it recursively so that we can properly install the bundle to the
		// correct path in the application.
		$target = dirname(path('bundle').$this->path($bundle));

		if ( ! is_dir($target))
		{
			mkdir($target, 0777, true);
		}
	}

	/**
	 * Return the path for a given bundle.
	 *
	 * @param  array   $bundle
	 * @return string
	 */
	protected function path($bundle)
	{
		return array_get($bundle, 'path', $bundle['name']);
	}

}