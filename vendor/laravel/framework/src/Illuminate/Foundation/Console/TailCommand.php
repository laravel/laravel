<?php namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class TailCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'tail';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = "Tail a log file on a remote server";

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$path = $this->getPath($this->argument('connection'));

		if ($path)
		{
			$this->tailLogFile($path, $this->argument('connection'));
		}
		else
		{
			$this->error('Could not determine path to log file.');
		}
	}

	/**
	 * Tail the given log file for the connection.
	 *
	 * @param  string  $path
	 * @param  string  $connection
	 * @return void
	 */
	protected function tailLogFile($path, $connection)
	{
		if (is_null($connection))
		{
			$this->tailLocalLogs($path);
		}
		else
		{
			$this->tailRemoteLogs($path, $connection);
		}
	}

	/**
	 * Tail a local log file for the application.
	 *
	 * @param  string  $path
	 * @return string
	 */
	protected function tailLocalLogs($path)
	{
		$output = $this->output;

		$lines = $this->option('lines');

		with(new Process('tail -f -n '.$lines.' '.$path))->setTimeout(null)->run(function($type, $line) use ($output)
		{
			$output->write($line);
		});
	}

	/**
	 * Tail a remote log file at the given path and connection.
	 *
	 * @param  string  $path
	 * @param  string  $connection
	 * @return void
	 */
	protected function tailRemoteLogs($path, $connection)
	{
		$out = $this->output;

		$lines = $this->option('lines');

		$this->getRemote($connection)->run('tail -f -n '.$lines.' '.$path, function($line) use ($out)
		{
			$out->write($line);
		});
	}

	/**
	 * Get a connection to the remote server.
	 *
	 * @param  string  $connection
	 * @return \Illuminate\Remote\Connection
	 */
	protected function getRemote($connection)
	{
		return $this->laravel['remote']->connection($connection);
	}

	/**
	 * Get the path to the Laravel log file.
	 *
	 * @param  string  $connection
	 * @return string
	 */
	protected function getPath($connection)
	{
		if ($this->option('path')) return $this->option('path');

		if (is_null($connection))
		{
			return base_path().'/app/storage/logs/laravel.log';
		}
		else
		{
			return $this->getRoot($connection).'/app/storage/logs/laravel.log';
		}
	}

	/**
	 * Get the path to the Laravel install root.
	 *
	 * @param  string  $connection
	 * @return string
	 */
	protected function getRoot($connection)
	{
		return $this->laravel['config']['remote.connections.'.$connection.'.root'];
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('connection', InputArgument::OPTIONAL, 'The remote connection name'),
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
			array('path', null, InputOption::VALUE_OPTIONAL, 'The fully qualified path to the log file.'),

			array('lines', null, InputOption::VALUE_OPTIONAL, 'The number of lines to tail.', 20),
		);
	}

}
