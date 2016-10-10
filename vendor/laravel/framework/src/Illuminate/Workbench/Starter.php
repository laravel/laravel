<?php namespace Illuminate\Workbench;

use Symfony\Component\Finder\Finder;
use Illuminate\Filesystem\Filesystem;

class Starter {

	/**
	 * Load the workbench vendor auto-load files.
	 *
	 * @param  string  $path
	 * @param  \Symfony\Component\Finder\Finder  $finder
	 * @param  \Illuminate\Filesystem\Filesystem  $files
	 * @return void
	 */
	public static function start($path, Finder $finder = null, Filesystem $files = null)
	{
		$finder = $finder ?: new Finder;

		// We will use the finder to locate all "autoload.php" files in the workbench
		// directory, then we will include them each so that they are able to load
		// the appropriate classes and file used by the given workbench package.
		$files = $files ?: new Filesystem;

		$autoloads = $finder->in($path)->files()->name('autoload.php')->depth('<= 3')->followLinks();

		foreach ($autoloads as $file)
		{
			$files->requireOnce($file->getRealPath());
		}
	}

}
