<?php namespace Laravel\CLI\Tasks;

use Laravel\Str;
use Laravel\File;

class Key extends Task {

	/**
	 * The path to the application config.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Create a new instance of the Key task.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->path = path('app').'config/application'.EXT;
	}

	/**
	 * Generate a random key for the application.
	 *
	 * @param  array  $arguments
	 * @return void
	 */
	public function generate($arguments = array())
	{
		// By default the Crypter class uses AES-256 encryption which uses
		// a 32 byte input vector, so that is the length of string we will
		// generate for the application token unless another length is
		// specified through the CLI.
		$key = Str::random(array_get($arguments, 0, 32));

		$config = File::get($this->path);

		$config = preg_replace("/('key'\s\=>\s'.*')/", "'key' => '{$key}'", $config, 1, $count);

		File::put($this->path, $config);


		echo "Configuration updated with secure key!";


		echo PHP_EOL;
	}

}
