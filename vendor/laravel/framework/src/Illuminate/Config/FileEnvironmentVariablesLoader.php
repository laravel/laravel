<?php namespace Illuminate\Config;

use Illuminate\Filesystem\Filesystem;

class FileEnvironmentVariablesLoader implements EnvironmentVariablesLoaderInterface {

	/**
	 * The filesystem instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * The path to the configuration files.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Create a new file environment loader instance.
	 *
	 * @param  \Illuminate\Filesystem\Filesystem  $files
	 * @return void
	 */
	public function __construct(Filesystem $files, $path = null)
	{
		$this->files = $files;
		$this->path = $path ?: base_path();
	}

	/**
	 * Load the environment variables for the given environment.
	 *
	 * @param  string  $environment
	 * @return array
	 */
	public function load($environment = null)
	{
		if ($environment == 'production') $environment = null;

		if ( ! $this->files->exists($path = $this->getFile($environment)))
		{
			return array();
		}
		else
		{
			return $this->files->getRequire($path);
		}
	}

	/**
	 * Get the file for the given environment.
	 *
	 * @param  string  $environment
	 * @return string
	 */
	protected function getFile($environment)
	{
		if ($environment)
		{
			return $this->path.'/.env.'.$environment.'.php';
		}
		else
		{
			return $this->path.'/.env.php';
		}
	}

}
