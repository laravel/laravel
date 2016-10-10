<?php namespace Illuminate\Queue\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class ForgetFailedCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'queue:forget';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Delete a failed queue job';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		if ($this->laravel['queue.failer']->forget($this->argument('id')))
		{
			$this->info('Failed job deleted successfully!');
		}
		else
		{
			$this->error('No failed job matches the given ID.');
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('id', InputArgument::REQUIRED, 'The ID of the failed job'),
		);
	}

}
