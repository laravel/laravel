<?php namespace Laravel\CLI\Tasks\Bundle;

use Laravel\File;
use Laravel\Bundle;
use FilesystemIterator;

class Publisher {

	/**
	 * Publish a bundle's assets to the public directory.
	 *
	 * @param  string  $bundle
	 * @return void
	 */
	public function publish($bundle)
	{
		$this->move($bundle, $this->from($bundle), $this->to($bundle));

		echo "Assets published for bundle [$bundle].".PHP_EOL;
	}

	/**
	 * Copy the contents of a bundle's assets to the public folder.
	 *
	 * @param  string  $bundle
	 * @param  string  $source
	 * @param  string  $destination
	 * @return void
	 */
	protected function move($bundle, $source, $destination)
	{
		File::copy_dir($source, $destination);	
	}

	/**
	 * Get the "to" location of the bundle's assets.
	 *
	 * @param  string  $bundle
	 * @return string
	 */
	protected function to($bundle)
	{
		return PUBLIC_PATH.'bundles'.DS.$bundle.DS;
	}

	/**
	 * Get the "from" location of the bundle's assets.
	 *
	 * @param  string  $bundle
	 * @return string
	 */
	protected function from($bundle)
	{
		return Bundle::path($bundle).'public';
	}

}