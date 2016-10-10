<?php namespace Illuminate\Config;

/**
 * PHP $_ENV loader for protecting sensitive configuration options.
 *
 * Inspired by the wonderful "Dotenv" library by Vance Lucas.
 */
class EnvironmentVariables {

	/**
	 * The environment loader implementation.
	 *
	 * @var \Illuminate\Config\EnvironmentLoaderInterface  $loader
	 */
	protected $loader;

	/**
	 * The server environment instance.
	 *
	 * @param  \Illuminate\Config\EnvironmentLoaderInterface  $loader
	 * @return void
	 */
	public function __construct(EnvironmentVariablesLoaderInterface $loader)
	{
		$this->loader = $loader;
	}

	/**
	 * Load the server variables for a given environment.
	 *
	 * @param  string  $environment
	 */
	public function load($environment = null)
	{
		foreach ($this->loader->load($environment) as $key => $value)
		{
			$_ENV[$key] = $value;

			$_SERVER[$key] = $value;

			putenv("{$key}={$value}");
		}
	}

}
