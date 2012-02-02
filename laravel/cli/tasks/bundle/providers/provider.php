<?php namespace Laravel\CLI\Tasks\Bundle\Providers;

use Laravel\File;

abstract class Provider {

	/**
	 * Install the given bundle into the application.
	 *
	 * @param  string  $bundle
	 * @return void
	 */
	abstract public function install($bundle);

	/**
	 * Install a bundle from by downloading a Zip.
	 *
	 * @param  array   $bundle
	 * @param  string  $url
	 * @return void
	 */
	protected function zipball($bundle, $url)
	{
		// When installing a bundle from a Zip archive, we'll first clone
		// down the bundle zip into the bundles "working" directory so
		// we have a spot to do all of our bundle extration work.
		$target = path('storage').'work/laravel-bundle.zip';

		File::put($target, file_get_contents($url));

		$zip = new \ZipArchive;

		$zip->open($target);

		// Once we have the Zip archive, we can open it and extract it
		// into the working directory. By convention, we expect the
		// archive to contain one root directory, and all of the
		// bundle contents should be stored in that directory.
		$zip->extractTo(path('storage').'work');

		$latest = File::latest(dirname($target));

		// Once we have the latest modified directory, we should be
		// able to move its contents over into the bundles folder
		// so the bundle will be usable by the develoepr.
		$path = $this->path($bundle);

		File::mvdir($latest->getRealPath(), path('bundle').$path);

		@unlink($target);
	}

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