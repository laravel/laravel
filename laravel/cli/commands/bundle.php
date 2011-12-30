<?php namespace Laravel\CLI\Commands; defined('APP_PATH') or die('No direct script access.');

class Bundle implements Command {

	/**
	 * The methods that the bundle command can handle.
	 *
	 * @var array
	 */
	protected $methods = array('install', 'upgrade', 'purge');

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

	protected function install($bundles)
	{
		foreach ($bundles as $bundle)
		{
			// Install the bundle...
		}
	}

	protected function upgrade($bundles)
	{
		//
	}

	protected function purge($bundles)
	{
		//
	}

}