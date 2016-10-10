<?php namespace Illuminate\Routing\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Routing\Generators\ControllerGenerator;

class MakeControllerCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'controller:make';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new resourceful controller';

	/**
	 * The controller generator instance.
	 *
	 * @var \Illuminate\Routing\Generators\ControllerGenerator
	 */
	protected $generator;

	/**
	 * The path to the controller directory.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Create a new make controller command instance.
	 *
	 * @param  \Illuminate\Routing\Generators\ControllerGenerator  $generator
	 * @param  string  $path
	 * @return void
	 */
	public function __construct(ControllerGenerator $generator, $path)
	{
		parent::__construct();

		$this->path = $path;
		$this->generator = $generator;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->generateController();
	}

	/**
	 * Generate a new resourceful controller file.
	 *
	 * @return void
	 */
	protected function generateController()
	{
		// Once we have the controller and resource that we are going to be generating
		// we will grab the path and options. We allow the developers to include or
		// exclude given methods from the resourceful controllers we're building.
		$controller = $this->input->getArgument('name');

		$path = $this->getPath();

		$options = $this->getBuildOptions();

		// Finally, we're ready to generate the actual controller file on disk and let
		// the developer start using it. The controller will be stored in the right
		// place based on the namespace of this controller specified by commands.
		$this->generator->make($controller, $path, $options);

		$this->info('Controller created successfully!');
	}

	/**
	 * Get the path in which to store the controller.
	 *
	 * @return string
	 */
	protected function getPath()
	{
		if ( ! is_null($this->input->getOption('path')))
		{
			return $this->laravel['path.base'].'/'.$this->input->getOption('path');
		}
		elseif ($bench = $this->input->getOption('bench'))
		{
			return $this->getWorkbenchPath($bench);
		}

		return $this->path;
	}

	/**
	 * Get the workbench path for the controller.
	 *
	 * @param  string  $bench
	 * @return string
	 */
	protected function getWorkbenchPath($bench)
	{
		$path = $this->laravel['path.base'].'/workbench/'.$bench.'/src/controllers';

		if ( ! $this->laravel['files']->isDirectory($path))
		{
			$this->laravel['files']->makeDirectory($path);
		}

		return $path;
	}

	/**
	 * Get the options for controller generation.
	 *
	 * @return array
	 */
	protected function getBuildOptions()
	{
		$only = $this->explodeOption('only');

		$except = $this->explodeOption('except');

		return compact('only', 'except');
	}

	/**
	 * Get and explode a given input option.
	 *
	 * @param  string  $name
	 * @return array
	 */
	protected function explodeOption($name)
	{
		$option = $this->input->getOption($name);

		return is_null($option) ? array() : explode(',', $option);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('name', InputArgument::REQUIRED, 'The name of the controller class'),
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
			array('bench', null, InputOption::VALUE_OPTIONAL, 'The workbench the controller belongs to'),

			array('only', null, InputOption::VALUE_OPTIONAL, 'The methods that should be included'),

			array('except', null, InputOption::VALUE_OPTIONAL, 'The methods that should be excluded'),

			array('path', null, InputOption::VALUE_OPTIONAL, 'Where to place the controller'),
		);
	}

}
