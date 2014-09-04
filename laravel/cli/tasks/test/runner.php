<?php namespace Laravel\CLI\Tasks\Test;

use Laravel\File;
use Laravel\Bundle;
use Laravel\Request;
use Laravel\CLI\Tasks\Task;

class Runner extends Task {

	/**
	 * The base directory where the tests will be executed.
	 *
	 * A phpunit.xml should also be stored in that directory.
	 * 
	 * @var string
	 */
	protected $base_path;

	/**
	 * Run all of the unit tests for the application.
	 *
	 * @param  array  $bundles
	 * @return void
	 */
	public function run($bundles = array())
	{
		if (count($bundles) == 0) $bundles = array(DEFAULT_BUNDLE);

		$this->bundle($bundles);
	}

	/**
	 * Run the tests for the Laravel framework.
	 *
	 * @return void
	 */
	public function core()
	{
		$this->base_path = path('sys').'tests'.DS;
		$this->stub(path('sys').'tests'.DS.'cases');

		$this->test();
	}

	/**
	 * Run the tests for a given bundle.
	 *
	 * @param  array  $bundles
	 * @return void
	 */
	public function bundle($bundles = array())
	{
		if (count($bundles) == 0)
		{
			$bundles = Bundle::names();
		}

		$this->base_path = path('sys').'cli'.DS.'tasks'.DS.'test'.DS;

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
		// pointing to our requested configuration file. This allows
		// us to flexibly run tests for any setup.
		$path = 'phpunit.xml';
		
		// fix the spaced directories problem when using the command line
		// strings with spaces inside should be wrapped in quotes.
		$esc_path = escapeshellarg($path);

		passthru('phpunit --configuration '.$esc_path, $status);

		@unlink($path);

		// Pass through the exit status
		exit($status);
	}

	/**
	 * Write a stub phpunit.xml file to the base directory.
	 *
	 * @param  string  $directory
	 * @return void
	 */
	protected function stub($directory)
	{
		$path = path('sys').'cli/tasks/test/';

		$stub = File::get($path.'stub.xml');

		// The PHPUnit bootstrap file contains several items that are swapped
		// at test time. This allows us to point PHPUnit at a few different
		// locations depending on what the developer wants to test.
		foreach (array('bootstrap', 'directory') as $item)
		{
			$stub = $this->{"swap_{$item}"}($stub, $directory);
		}

		File::put(path('base').'phpunit.xml', $stub);
	}

	/**
	 * Swap the bootstrap file in the stub.
	 *
	 * @param  string  $stub
	 * @param  string  $directory
	 * @return string
	 */
	protected function swap_bootstrap($stub, $directory)
	{
		return str_replace('{{bootstrap}}', $this->base_path.'phpunit.php', $stub);
	}

	/**
	 * Swap the directory in the stub.
	 *
	 * @param  string  $stub
	 * @param  string  $directory
	 * @return string
	 */
	protected function swap_directory($stub, $directory)
	{
		return str_replace('{{directory}}', $directory, $stub);
	}

}