<?php namespace Laravel\CLI\Commands; defined('APP_PATH') or die('No direct script access.');

use Laravel\IoC;

IoC::register('bundle.provider: github', function()
{
	return new \Laravel\CLI\Bundle\Providers\Github;
});

class Bundle implements Command {

	/**
	 * An instance of the Bundle API repository.
	 *
	 * @var Bundle\API
	 */
	protected $repository;

	/**
	 * The methods that the bundle command can handle.
	 *
	 * @var array
	 */
	protected $methods = array('install');

	/**
	 * Create a new instance of the Bundle CLI command.
	 *
	 * @param  Bundle\Repository
	 * @return void
	 */
	public function __construct($repository)
	{
		$this->repository = $repository;
	}

	/**
	 * Execute a bundle command from the CLI.
	 *
	 * @param  array  $arguments
	 * @return void
	 */
	public function run($arguments = array())
	{
		$method = array_get($arguments, 0);

		if ( ! in_array($method, $this->methods))
		{
			throw new \Exception("I don't recognize that bundle command.");
		}

		$this->$method(array_slice($arguments, 1));
	}

	/**
	 * Install the given bundles into the application.
	 *
	 * @param  array  $bundles
	 * @return void
	 */
	protected function install($bundles)
	{
		foreach ($bundles as $bundle)
		{
			if (is_dir(BUNDLE_PATH.$bundle))
			{
				echo "Bundle {$bundle['name']} is already installed.";

				continue;
			}

			// First we'll retrieve the bundle information array from the bundle
			// repository. This array contains information such as the provider
			// for the bundle, and any dependencies it may have.
			$bundle = $this->repository->get($bundle);

			if ( ! $bundle)
			{
				throw new \Exception("The bundle API is not responding.");
			}

			// Once we have the bundle information, we can resolve an instance
			// of a provider and install the bundle into the application and
			// all of its registered dependencies as well.
			//
			// Each bundle provider implements the Provider interface and
			// is repsonsible for retrieving the bundle source from its
			// hosting party and installing it into the application.
			$provider = "bundle.provider: {$bundle['provider']}";

			IoC::resolve($provider)->install($bundle);

			$this->install($bundle['dependencies']);
		}
	}

}