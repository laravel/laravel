<?php namespace Laravel\CLI\Tasks\Bundle; defined('APP_PATH') or die('No direct script access.');

use Laravel\IoC;
use Laravel\Bundle;
use Laravel\CLI\Tasks\Task;

class Bundler extends Task {

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
	 * Publish bundle assets to the public directory.
	 *
	 * @param  array  $bundles
	 * @return void
	 */
	public function publish($bundles)
	{
		// If no bundles are passed to the command, we'll just gather all
		// of the installed bundle names and publish the assets for each
		// of the bundles to the public directory.
		if (count($bundles) == 0) $bundles = Bundle::all();

		$publisher = IoC::resolve('bundle.publisher');

		foreach ($bundles as $bundle)
		{
			$publisher->publish($bundle['name']);
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

		$repository = IoC::resolve('bundle.repository');

		foreach ($bundles as $bundle)
		{
			// First we'll call the bundle repository to gather the bundle data
			// array, which contains all of the information needed to install
			// the bundle into the application. We'll verify that the bundle
			// exists and the API is responding for each bundle.
			$response = $repository->get($bundle);

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