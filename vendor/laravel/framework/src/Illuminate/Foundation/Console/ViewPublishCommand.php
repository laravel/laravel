<?php namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Foundation\ViewPublisher;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ViewPublishCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'view:publish';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = "Publish a package's views to the application";

	/**
	 * The view publisher instance.
	 *
	 * @var \Illuminate\Foundation\ViewPublisher
	 */
	protected $view;

	/**
	 * Create a new view publish command instance.
	 *
	 * @param  \Illuminate\Foundation\ViewPublisher  $view
	 * @return void
	 */
	public function __construct(ViewPublisher $view)
	{
		parent::__construct();

		$this->view = $view;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$package = $this->input->getArgument('package');

		if ( ! is_null($path = $this->getPath()))
		{
			$this->view->publish($package, $path);
		}
		else
		{
			$this->view->publishPackage($package);
		}

		$this->output->writeln('<info>Views published for package:</info> '.$package);
	}

	/**
	 * Get the specified path to the files.
	 *
	 * @return string
	 */
	protected function getPath()
	{
		$path = $this->input->getOption('path');

		if ( ! is_null($path))
		{
			return $this->laravel['path.base'].'/'.$path;
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
			array('package', InputArgument::REQUIRED, 'The name of the package being published.'),
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
			array('path', null, InputOption::VALUE_OPTIONAL, 'The path to the source view files.', null),
		);
	}

}
