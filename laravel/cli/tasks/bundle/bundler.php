<?php namespace Laravel\CLI\Tasks\Bundle; defined('DS') or die('No direct script access.');

use Laravel\IoC;
use Laravel\Bundle;
use Laravel\CLI\Tasks\Task;

class Bundler extends Task {

	/**
	 * The bundle API repository.
	 *
	 * @var Repository
	 */
	protected $repository;

	/**
	 * Create a new bundle manager task.
	 *
	 * @param  Repository  $repository
	 * @return void
	 */
	public function __construct($repository)
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
			if (is_dir(path('bundle').$bundle['name']))
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
			$this->download($bundle, $this->path($bundle));

			echo "Bundle [{$bundle['name']}] has been installed!".PHP_EOL;
		}
	}

	/**
	 * Upgrade the given bundles for the application.
	 *
	 * @param  array  $bundles
	 * @return void
	 */
	public function upgrade($bundles)
	{
		foreach ($bundles as $name)
		{
			$bundle = Bundle::get($name);

			if (is_nulL($bundle))
			{
				throw new \Exception("Bundle [{$name}] is not installed!");
			}

			$data = $this->retrieve($bundle);

			if ($response['status'] == 'not-found')
			{
				continue;
			}

			File::rmdir($bundle->location);

			$this->download($bundle, $bundle->location);

			echo "Bundle [{$bundle['name']}] has been upgraded!".PHP_EOL;
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
		if (count($bundles) == 0) $bundles = Bundle::names();

		$publisher = IoC::resolve('bundle.publisher');

		foreach ($bundles as $bundle)
		{
			$publisher->publish($bundle);
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

		foreach ($bundles as $bundle)
		{
			// First we'll call the bundle repository to gather the bundle data
			// array, which contains all of the information needed to install
			// the bundle into the application. We'll verify that the bundle
			// exists and the API is responding for each bundle.
			$response = $this->retrieve($bundle);

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

	/**
	 * Install a bundle using a provider.
	 *
	 * @param  string  $bundle
	 * @param  string  $path
	 * @return void
	 */
	protected function download($bundlem, $path)
	{
		$provider = "bundle.provider: {$bundle['provider']}";

		IoC::resolve($provider)->install($bundle, $path);
	}

	/**
	 * Retrieve a bundle from the repository.
	 *
	 * @param  string  $bundle
	 * @return array
	 */
	protected function retrieve($bundle)
	{
		$response = $this->repository->get($bundle);

		if ( ! $response)
		{
			throw new \Exception("The bundle API is not responding.");
		}

		return $response;
	}

	/**
	 * Return the path for a given bundle.
	 *
	 * @param  array   $bundle
	 * @return string
	 */
	protected function path($bundle)
	{
		return array_get($bundle, 'path', $bundle['name']);
	}

}