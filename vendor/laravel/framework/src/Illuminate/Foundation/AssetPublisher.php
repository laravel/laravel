<?php namespace Illuminate\Foundation;

use Illuminate\Filesystem\Filesystem;

class AssetPublisher {

	/**
	 * The filesystem instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * The path where assets should be published.
	 *
	 * @var string
	 */
	protected $publishPath;

	/**
	 * The path where packages are located.
	 *
	 * @var string
	 */
	protected $packagePath;

	/**
	 * Create a new asset publisher instance.
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
	 * Copy all assets from a given path to the publish path.
	 *
	 * @param  string  $name
	 * @param  string  $source
	 * @return bool
	 *
	 * @throws \RuntimeException
	 */
	public function publish($name, $source)
	{
		$destination = $this->publishPath."/packages/{$name}";

		$success = $this->files->copyDirectory($source, $destination);

		if ( ! $success)
		{
			throw new \RuntimeException("Unable to publish assets.");
		}

		return $success;
	}

	/**
	 * Publish a given package's assets to the publish path.
	 *
	 * @param  string  $package
	 * @param  string  $packagePath
	 * @return bool
	 */
	public function publishPackage($package, $packagePath = null)
	{
		$packagePath = $packagePath ?: $this->packagePath;

		// Once we have the package path we can just create the source and destination
		// path and copy the directory from one to the other. The directory copy is
		// recursive so all nested files and directories will get copied as well.
		$source = $packagePath."/{$package}/public";

		return $this->publish($package, $source);
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
