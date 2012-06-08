<?php namespace Laravel\CLI\Tasks;

use Laravel\Str;
use Laravel\File;
use Laravel\Config as Config;
use Laravel\CLI\Command as Command;

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
		$current = Config::get('application.key');

		$config = File::get($this->path);

		$config = str_replace("'key' => '',", "'key' => '{$key}',", $config, $count);

		if ($count == 0)
		{
			echo "An application key already exists!" . PHP_EOL;
			echo "Would you like to override the existing key?" . PHP_EOL;
			echo "yes/no: ";

			// Require an bool value.
			$rules['input'] = 'required|in:yes';
			$messages = array('in' => '');

			// Validate input and handle response.
			if (Command::input($rules,$messages)) {
				$config = str_replace("'key' => '{$current}',", "'key' => '{$key}',", $config, $count);
				echo "Configuration updated with secure key!";
			}
		}
		else
		{
			echo "Configuration updated with secure key!";
		}

		File::put($this->path, $config);

		echo PHP_EOL;
	}

}