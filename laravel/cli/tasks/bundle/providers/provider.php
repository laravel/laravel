<?php namespace Laravel\CLI\Tasks\Bundle\Providers;

use Laravel\File;

abstract class Provider {

	/**
	 * Install the given bundle into the application.
	 *
	 * @param  string  $bundle
	 * @param  string  $path
	 * @return void
	 */
	abstract public function install($bundle, $path);

	/**
	 * Install a bundle from by downloading a Zip.
	 *
	 * @param  string  $url
	 * @param  array   $bundle
	 * @param  string  $path
	 * @return void
	 */
	protected function zipball($url, $bundle, $path)
	{
		$work = path('storage').'work/';

		// When installing a bundle from a Zip archive, we'll first clone
		// down the bundle zip into the bundles "working" directory so
		// we have a spot to do all of our bundle extration work.
		$target = $work.'laravel-bundle.zip';

		File::put($target, file_get_contents($url));

		$zip = new \ZipArchive;

		$zip->open($target);

		// Once we have the Zip archive, we can open it and extract it
		// into the working directory. By convention, we expect the
		// archive to contain one root directory, and all of the
		// bundle contents should be stored in that directory.
		$zip->extractTo($work);

		$latest = File::latest($work)->getRealPath();

		@chmod($latest, 0777);

		// Once we have the latest modified directory, we should be
		// able to move its contents over into the bundles folder
		// so the bundle will be usable by the develoepr.
		$path = $this->path($bundle);

		File::mvdir($latest, path('bundle').$path);

		@unlink($target);
	}

}