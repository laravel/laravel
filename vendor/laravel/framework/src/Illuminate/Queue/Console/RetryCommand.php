<?php namespace Illuminate\Queue\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class RetryCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'queue:retry';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Retry a failed queue job';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$failed = $this->laravel['queue.failer']->find($this->argument('id'));

		if ( ! is_null($failed))
		{
			$failed->payload = $this->resetAttempts($failed->payload);

			$this->laravel['queue']->connection($failed->connection)->pushRaw($failed->payload, $failed->queue);

			$this->laravel['queue.failer']->forget($failed->id);

			$this->info('The failed job has been pushed back onto the queue!');
		}
		else
		{
			$this->error('No failed job matches the given ID.');
		}
	}

	/**
	 * Reset the payload attempts.
	 *
	 * @param  string  $payload
	 * @return string
	 */
	protected function resetAttempts($payload)
	{
		$payload = json_decode($payload, true);

		if (isset($payload['attempts'])) $payload['attempts'] = 0;

		return json_encode($payload);
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
