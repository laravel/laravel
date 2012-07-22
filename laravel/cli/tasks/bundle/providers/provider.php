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
		// we have a spot to do all of our bundle extraction work.
		$target = $work.'laravel-bundle.zip';

		File::put($target, $this->download($url));

		$zip = new \ZipArchive;

		$zip->open($target);

		// Once we have the Zip archive, we can open it and extract it
		// into the working directory. By convention, we expect the
		// archive to contain one root directory with the bundle.
		mkdir($work.'zip');

		$zip->extractTo($work.'zip');

		$latest = File::latest($work.'zip')->getRealPath();

		@chmod($latest, 0777);

		// Once we have the latest modified directory, we should be
		// able to move its contents over into the bundles folder
		// so the bundle will be usable by the developer.
		File::mvdir($latest, $path);

		File::rmdir($work.'zip');

		$zip->close();
		@unlink($target);
	}

	/**
	 * Download a remote zip archive from a URL.
	 *
	 * @param  string  $url
	 * @return string
	 */
	protected function download($url)
	{
		$remote = file_get_contents($url);

		// If we were unable to download the zip archive correctly
		// we'll bomb out since we don't want to extract the last
		// zip that was put in the storage directory.
		if ($remote === false)
		{
			throw new \Exception("Error downloading bundle [{$bundle}].");
		}

		return $remote;
	}

}