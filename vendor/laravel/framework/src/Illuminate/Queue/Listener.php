<?php namespace Illuminate\Queue;

use Symfony\Component\Process\Process;

class Listener {

	/**
	 * The command working path.
	 *
	 * @var string
	 */
	protected $commandPath;

	/**
	 * The environment the workers should run under.
	 *
	 * @var string
	 */
	protected $environment;

	/**
	 * The amount of seconds to wait before polling the queue.
	 *
	 * @var  int
	 */
	protected $sleep = 3;

	/**
	 * The amount of times to try a job before logging it failed.
	 *
	 * @var  int
	 */
	protected $maxTries = 0;

	/**
	 * The queue worker command line.
	 *
	 * @var string
	 */
	protected $workerCommand = 'php artisan queue:work %s --queue="%s" --delay=%s --memory=%s --sleep=%s --tries=%s';

	/**
	 * Create a new queue listener.
	 *
	 * @param  string  $commandPath
	 * @param  string  $environment
	 * @return void
	 */
	public function __construct($commandPath, $environment = null)
	{
		$this->commandPath = $commandPath;
		$this->environment = $environment;
	}

	/**
	 * Listen to the given queue connection.
	 *
	 * @param  string  $connection
	 * @param  string  $queue
	 * @param  string  $delay
	 * @param  string  $memory
	 * @param  int     $timeout
	 * @return void
	 */
	public function listen($connection, $queue, $delay, $memory, $timeout = 60)
	{
		$process = $this->makeProcess($connection, $queue, $delay, $memory, $timeout);

		while(true)
		{
			$this->runProcess($process, $memory);
		}
	}

	/**
	 * Run the given process.
	 *
	 * @param  \Symfony\Component\Process\Process  $process
	 * @param  int  $memory
	 * @return void
	 */
	public function runProcess(Process $process, $memory)
	{
		$process->run();

		// Once we have run the job we'll go check if the memory limit has been
		// exceeded for the script. If it has, we will kill this script so a
		// process managers will restart this with a clean slate of memory.
		if ($this->memoryExceeded($memory))
		{
			$this->stop(); return;
		}
	}

	/**
	 * Create a new Symfony process for the worker.
	 *
	 * @param  string  $connection
	 * @param  string  $queue
	 * @param  int     $delay
	 * @param  int     $memory
	 * @param  int     $timeout
	 * @return \Symfony\Component\Process\Process
	 */
	public function makeProcess($connection, $queue, $delay, $memory, $timeout)
	{
		$string = $this->workerCommand;

		// If the environment is set, we will append it to the command string so the
		// workers will run under the specified environment. Otherwise, they will
		// just run under the production environment which is not always right.
		if (isset($this->environment))
		{
			$string .= ' --env='.$this->environment;
		}

		// Next, we will just format out the worker commands with all of the various
		// options available for the command. This will produce the final command
		// line that we will pass into a Symfony process object for processing.
		$command = sprintf(
			$string, $connection, $queue, $delay,
			$memory, $this->sleep, $this->maxTries
		);

		return new Process($command, $this->commandPath, null, null, $timeout);
	}

	/**
	 * Determine if the memory limit has been exceeded.
	 *
	 * @param  int   $memoryLimit
	 * @return bool
	 */
	public function memoryExceeded($memoryLimit)
	{
		return (memory_get_usage() / 1024 / 1024) >= $memoryLimit;
	}

	/**
	 * Stop listening and bail out of the script.
	 *
	 * @return void
	 */
	public function stop()
	{
		die;
	}

	/**
	 * Get the current listener environment.
	 *
	 * @return string
	 */
	public function getEnvironment()
	{
		return $this->environment;
	}

	/**
	 * Set the current environment.
	 *
	 * @param  string  $environment
	 * @return void
	 */
	public function setEnvironment($environment)
	{
		$this->environment = $environment;
	}

	/**
	 * Get the amount of seconds to wait before polling the queue.
	 *
	 * @return int
	 */
	public function getSleep()
	{
		return $this->sleep;
	}

	/**
	 * Set the amount of seconds to wait before polling the queue.
	 *
	 * @param  int  $sleep
	 * @return void
	 */
	public function setSleep($sleep)
	{
		$this->sleep = $sleep;
	}

	/**
	 * Set the amount of times to try a job before logging it failed.
	 *
	 * @param  int  $tries
	 * @return void
	 */
	public function setMaxTries($tries)
	{
		$this->maxTries = $tries;
	}

}
