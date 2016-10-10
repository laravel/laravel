<?php namespace Illuminate\Foundation;

use Illuminate\Filesystem\Filesystem;

class ConfigPublisher {

	/**
	 * The filesystem instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * The destination of the config files.
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
	 * Create a new configuration publisher instance.
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
	 * Publish configuration files from a given path.
	 *
	 * @param  string  $package
	 * @param  string  $source
	 * @return bool
	 */
	public function publish($package, $source)
	{
		$destination = $this->publishPath."/packages/{$package}";

		$this->makeDestination($destination);

		return $this->files->copyDirectory($source, $destination);
	}

	/**
	 * Publish the configuration files for a package.
	 *
	 * @param  string  $package
	 * @param  string  $packagePath
	 * @return bool
	 */
	public function publishPackage($package, $packagePath = null)
	{
		// First we will figure out the source of the package's configuration location
		// which we do by convention. Once we have that we will move the files over
		// to the "main" configuration directory for this particular application.
		$path = $packagePath ?: $this->packagePath;

		$source = $this->getSource($package, $path);

		return $this->publish($package, $source);
	}

	/**
	 * Get the source configuration directory to publish.
	 *
	 * @param  string  $package
	 * @param  string  $packagePath
	 * @return string
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function getSource($package, $packagePath)
	{
		$source = $packagePath."/{$package}/src/config";

		if ( ! $this->files->isDirectory($source))
		{
			throw new \InvalidArgumentException("Configuration not found.");
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
