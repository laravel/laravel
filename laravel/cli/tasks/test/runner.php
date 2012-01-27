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
	 * Run the tests for a given bundle.
	 *
	 * @param  array  $arguments
	 * @return void
	 */
	public function bundle($arguments = array())
	{
		// To run PHPUnit for the application, bundles, and the framework
		// from one task, we'll dynamically stub PHPUnit.xml files via
		// the task and point the test suite to the correct directory
		// based on what was requested.
		$this->stub(Bundle::path($arguments[0]).'tests');

		$this->test();
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