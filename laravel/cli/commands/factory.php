<?php namespace Laravel\CLI\Commands;

use Laravel\IoC;

class Factory {

	/**
	 * Create a CLI command instance.
	 *
	 * @param  string   $command
	 * @return Command
	 */
	public static function make($command)
	{
		switch ($command)
		{
			case 'task':
				return new Task;
		}
	}

}