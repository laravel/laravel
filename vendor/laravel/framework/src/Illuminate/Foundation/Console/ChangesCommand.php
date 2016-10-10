<?php namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class ChangesCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'changes';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = "Display the framework change list";

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		list($version, $changes) = $this->getChangeVersion($this->getChangesArray());

		$this->writeHeader($version);

		foreach ($changes as $change)
		{
			$this->line($this->formatMessage($change));
		}
	}

	/**
	 * Write the heading for the change log.
	 *
	 * @param  string  $version
	 * @return void
	 */
	protected function writeHeader($version)
	{
		$this->info($heading = 'Changes For Laravel '.$version);

		$this->comment(str_repeat('-', strlen($heading)));
	}

	/**
	 * Format the given change message.
	 *
	 * @param  array   $change
	 * @return string
	 */
	protected function formatMessage(array $change)
	{
		$message = '<comment>-></comment> <info>'.$change['message'].'</info>';

		if ( ! is_null($change['backport']))
		{
			$message .= ' <comment>(Backported to '.$change['backport'].')</comment>';
		}

		return $message;
	}

	/**
	 * Get the change list for the specified version.
	 *
	 * @param  array  $changes
	 * @return array
	 */
	protected function getChangeVersion(array $changes)
	{
		$version = $this->argument('version');

		if (is_null($version))
		{
			$latest = head(array_keys($changes));

			return array($latest, $changes[$latest]);
		}
		else
		{
			return array($version, array_get($changes, $version, array()));
		}
	}

	/**
	 * Get the changes array from disk.
	 *
	 * @return array
	 */
	protected function getChangesArray()
	{
		return json_decode(file_get_contents(__DIR__.'/../changes.json'), true);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('version', InputArgument::OPTIONAL, 'The version to list changes for.'),
		);
	}

}
