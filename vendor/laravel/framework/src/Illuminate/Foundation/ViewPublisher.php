<?php namespace Illuminate\Foundation;

use Illuminate\Filesystem\Filesystem;

class ViewPublisher {

	/**
	 * The filesystem instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * The destination of the view files.
	 *
	 * @var string
	 */
	protected $publishPath;

	/**
	 * The path to the application's packages.
	 *
	 * @var string
	 */
	protected $packagePath;

	/**
	 * Create a new view publisher instance.
	 *
	 * @param  \Illuminate\Filesystem\Filesystem  $files
	 * @param  string  $publishPath
	 * @return void
	 */
	public function __construct(Filesystem $files, $publishPath)
	{
		$this->files = $files;
		$this->publishPath = $publishPath;
	}

	/**
	 * Publish view files from a given path.
	 *
	 * @param  string  $package
	 * @param  string  $source
	 * @return void
	 */
	public function publish($package, $source)
	{
		$destination = $this->publishPath."/packages/{$package}";

		$this->makeDestination($destination);

		return $this->files->copyDirectory($source, $destination);
	}

	/**
	 * Publish the view files for a package.
	 *
	 * @param  string  $package
	 * @param  string  $packagePath
	 * @return void
	 */
	public function publishPackage($package, $packagePath = null)
	{
		$source = $this->getSource($package, $packagePath ?: $this->packagePath);

		return $this->publish($package, $source);
	}

	/**
	 * Get the source views directory to publish.
	 *
	 * @param  string  $package
	 * @param  string  $packagePath
	 * @return string
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function getSource($package, $packagePath)
	{
		$source = $packagePath."/{$package}/src/views";

		if ( ! $this->files->isDirectory($source))
		{
			throw new \InvalidArgumentException("Views not found.");
		}

		return $source;
	}

	/**
	 * Create the destination directory if it doesn't exist.
	 *
	 * @param  string  $destination
	 * @return void
	 */
	protected function makeDestination($destination)
	{
		if ( ! $this->files->isDirectory($destination))
		{
			$this->files->makeDirectory($destination, 0777, true);
		}
	}

	/**
	 * Set the default package path.
	 *
	 * @param  string  $packagePath
	 * @return void
	 */
	public function setPackagePath($packagePath)
	{
		$this->packagePath = $packagePath;
	}

}
