<?php namespace Laravel\CLI\Tasks\Test;

use Laravel\File;
use Laravel\Bundle;
use Laravel\CLI\Tasks\Task;

class Runner extends Task {

	/**
	 * Run all of the unit tests for the application.
	 *
	 * @return void
	 */
	public function run()
	{
		$this->bundle(array(DEFAULT_BUNDLE));
	}

	/**
	 * Run the tests for the Laravel framework.
	 *
	 * @return void
	 */
	public function core()
	{
		if ( ! is_dir(BUNDLE_PATH.'laravel-tests'))
		{
			throw new \Exception("The bundle [laravel-tests] has not been installed!");
		}

		// When testing the Laravel core, we will just stub the path directly
		// so the test bundle is not required to be registered in the bundle
		// configuration, as it is kind of a unique bundle.
		$this->stub(BUNDLE_PATH.'laravel-tests/cases');

		$this->test();
	}

	/**
	 * Run the tests for a given bundle.
	 *
	 * @param  array  $arguments
	 * @return void
	 */
	public function bundle($bundles = array())
	{
		if (count($bundles) == 0)
		{
			$bundles = Bundle::names();
		}

		foreach ($bundles as $bundle)
		{
			// To run PHPUnit for the application, bundles, and the framework
			// from one task, we'll dynamically stub PHPUnit.xml files via
			// the task and point the test suite to the correct directory
			// based on what was requested.
			if (is_dir($path = Bundle::path($bundle).'tests'))
			{
				$this->stub($path);

				$this->test();				
			}
		}
	}

	/**
	 * Run PHPUnit with the temporary XML configuration.
	 *
	 * @return void
	 */
	protected function test()
	{
		// We'll simply fire off PHPUnit with the configuration switch
		// pointing to our temporary configuration file. This allows
		// us to flexibly run tests for any setup.
		passthru('phpunit -c '.BASE_PATH.'phpunit.xml');

		@unlink(BASE_PATH.'phpunit.xml');
	}

	/**
	 * Write a stub phpunit.xml file to the base directory.
	 *
	 * @param  string  $directory
	 * @return void
	 */
	protected function stub($directory)
	{
		$stub = File::get(SYS_PATH.'cli/tasks/test/stub.xml');

		$stub = str_replace('{{directory}}', $directory, $stub);

		File::put(BASE_PATH.'phpunit.xml', $stub);
	}

}