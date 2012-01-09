<?php namespace Laravel\CLI\Tasks\Bundle; defined('APP_PATH') or die('No direct script access.');

use Laravel\IoC;
use Laravel\CLI\Tasks\Task;

IoC::register('bundle.provider: github', function()
{
	return new \Laravel\CLI\Tasks\Bundle\Providers\Github;
});

class Installer extends Task {

	/**
	 * An instance of the Bundle API repository.
	 *
	 * @var Repository
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
	 * @param  Repository
	 * @return void
	 */
	public function __construct(Repository $repository)
	{
		$this->repository = $repository;
	}

	/**
	 * Install the given bundles into the application.
	 *
	 * @param  array  $bundles
	 * @return void
	 */
	public function install($bundles)
	{
		foreach ($this->get($bundles) as $bundle)
		{
			if (is_dir(BUNDLE_PATH.$bundle['name']))
			{
				echo "Bundle {$bundle['name']} is already installed.";

				continue;
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
		}
	}

	/**
	 * Gather all of the bundles from the bundle repository.
	 *
	 * @param  array  $bundles
	 * @return array
	 */
	protected function get($bundles)
	{
		$responses = array();

		// This method is primarily responsible for gathering the data
		// for all bundles that need to be installed. This allows us
		// to verify the existence of the bundle before even getting
		// started on the actual installation process.
		foreach ($bundles as $bundle)
		{
			// First, we'll call the bundle repository to gather the bundle data
			// array, which contains all of the information needed to install
			// the bundle into the application. We'll verify that the bundle
			// exists and that the bundle API is responding for each bundle.
			$response = $this->repository->get($bundle);

			if ( ! $response)
			{
				throw new \Exception("The bundle API is not responding.");
			}

			if ($response['status'] == 'not-found')
			{
				throw new \Exception("There is not a bundle named [$bundle].");
			}

			// If the bundle was retrieved successfully, we will add it to
			// our array of bundles, as well as merge all of the bundle's
			// dependencies into the array of responses so that they are
			// installed along with the consuming dependency.
			$bundle = $response['bundle'];

			$responses[] = $bundle;

			$responses = array_merge($responses, $this->get($bundle['dependencies']));
		}

		return $responses;
	}

}