<?php namespace Laravel\CLI\Tasks\Bundle; defined('DS') or die('No direct script access.');

use Laravel\IoC;
use Laravel\File;
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
			if (Bundle::exists($bundle['name']))
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
			$path = path('bundle').$this->path($bundle);

			$this->download($bundle, $path);

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
		if (count($bundles) == 0) $bundles = Bundle::names();

		foreach ($bundles as $name)
		{
			if ( ! Bundle::exists($name))
			{
				echo "Bundle [{$name}] is not installed!";

				continue;
			}

			// First we want to retrieve the information for the bundle,
			// such as where it is currently installed. This will let
			// us upgrade the bundle into the same path in which it
			// is already installed.
			$bundle = Bundle::get($name);

			// If the bundle exists, we will grab the data about the
			// bundle from the API so we can make the right bundle
			// provider for the bundle, since we have no way of
			// knowing which provider was used to install.
			$response = $this->retrieve($name);

			if ($response['status'] == 'not-found')
			{
				continue;
			}

			// Once we have the bundle information from the API,
			// we'll simply recursively delete the bundle and
			// then re-download it using the provider.
			File::rmdir($bundle->location);

			$this->download($response['bundle'], $bundle->location);

			echo "Bundle [{$name}] has been upgraded!".PHP_EOL;
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
		// If no bundles are passed to the command, we'll just gather
		// all of the installed bundle names and publish the assets
		// for each of the bundles to the public directory.
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

			$dependencies = $this->get($bundle['dependencies']);

			$responses = array_merge($responses, $dependencies);
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
	protected function download($bundle, $path)
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