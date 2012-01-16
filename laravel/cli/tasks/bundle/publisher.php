<?php namespace Laravel\CLI\Tasks\Bundle;

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
		if ( ! is_dir($source)) return;

		// First we need to create the destination directory if it doesn't
		// already exists. This directory hosts all of the assets we copy
		// from the installed bundle's source directory.
		if ( ! is_dir($destination))
		{
			mkdir($destination);
		}

		$items = new FilesystemIterator($source, FilesystemIterator::SKIP_DOTS);

		foreach ($items as $item)
		{
			// If the file system item is a directory, we will recurse the
			// function, passing in the item directory. To get the proper
			// destination path, we'll replace the root bundle asset
			// directory with the root public asset directory.
			if ($item->isDir())
			{
				$path = $item->getRealPath();

				$recurse = str_replace($this->from($bundle), $this->to($bundle), $path);

				$this->move($bundle, $path, $recurse);
			}
			// If the file system item is an actual file, we can copy the
			// file from the bundle asset directory to the public asset
			// directory. The "copy" method will overwrite any existing
			// files with the same name.
			else
			{
				copy($item->getRealPath(), $destination.DS.$item->getBasename());
			}
		}		
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