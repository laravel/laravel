<?php namespace Illuminate\Queue\Console;

use Illuminate\Queue\Worker;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class WorkCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'queue:work';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Process the next job on a queue';

	/**
	 * The queue listener instance.
	 *
	 * @var \Illuminate\Queue\Listener
	 */
	protected $worker;

	/**
	 * Create a new queue listen command.
	 *
	 * @param  \Illuminate\Queue\Worker  $worker
	 * @return void
	 */
	public function __construct(Worker $worker)
	{
		parent::__construct();

		$this->worker = $worker;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		if ($this->downForMaintenance()) return;

		$queue = $this->option('queue');

		$delay = $this->option('delay');

		// The memory limit is the amount of memory we will allow the script to occupy
		// before killing it and letting a process manager restart it for us, which
		// is to protect us against any memory leaks that will be in the scripts.
		$memory = $this->option('memory');

		$connection = $this->argument('connection');

		$this->worker->pop($connection, $queue, $delay, $memory, $this->option('sleep'), $this->option('tries'));
	}

	/**
	 * Determine if the worker should run in maintenance mode.
	 *
	 * @return bool
	 */
	protected function downForMaintenance()
	{
		if ($this->option('force')) return false;

		return $this->laravel->isDownForMaintenance();
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('connection', InputArgument::OPTIONAL, 'The name of connection', null),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('queue', null, InputOption::VALUE_OPTIONAL, 'The queue to listen on'),

			array('delay', null, InputOption::VALUE_OPTIONAL, 'Amount of time to delay failed jobs', 0),

			array('force', null, InputOption::VALUE_NONE, 'Force the worker to run even in maintenance mode'),

			array('memory', null, InputOption::VALUE_OPTIONAL, 'The memory limit in megabytes', 128),

			array('sleep', null, InputOption::VALUE_OPTIONAL, 'Number of seconds to sleep when no job is available', 3),

			array('tries', null, InputOption::VALUE_OPTIONAL, 'Number of times to attempt a job before logging it failed', 0),
		);
	}

}
