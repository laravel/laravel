<?php namespace Illuminate\Auth\Console;

use Illuminate\Console\Command;

class ClearRemindersCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'auth:clear-reminders';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Flush expired reminders.';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->laravel['auth.reminder.repository']->deleteExpired();

		$this->info('Expired reminders cleared!');
	}

}
