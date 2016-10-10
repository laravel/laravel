<?php namespace Illuminate\Workbench\Console;

use Illuminate\Console\Command;
use Illuminate\Workbench\Package;
use Illuminate\Workbench\PackageCreator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class WorkbenchMakeCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'workbench';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new package workbench';

	/**
	 * The package creator instance.
	 *
	 * @var \Illuminate\Workbench\PackageCreator
	 */
	protected $creator;

	/**
	 * Create a new make workbench command instance.
	 *
	 * @param  \Illuminate\Workbench\PackageCreator  $creator
	 * @return void
	 */
	public function __construct(PackageCreator $creator)
	{
		parent::__construct();

		$this->creator = $creator;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$workbench = $this->runCreator($this->buildPackage());

		$this->info('Package workbench created!');

		$this->callComposerUpdate($workbench);
	}

	/**
	 * Run the package creator class for a given Package.
	 *
	 * @param  \Illuminate\Workbench\Package  $package
	 * @return string
	 */
	protected function runCreator($package)
	{
		$path = $this->laravel['path.base'].'/workbench';

		$plain = ! $this->option('resources');

		return $this->creator->create($package, $path, $plain);
	}

	/**
	 * Call the composer update routine on the path.
	 *
	 * @param  string  $path
	 * @return void
	 */
	protected function callComposerUpdate($path)
	{
		chdir($path);

		passthru('composer install --dev');
	}

	/**
	 * Build the package details from user input.
	 *
	 * @return \Illuminate\Workbench\Package
	 */
	protected function buildPackage()
	{
		list($vendor, $name) = $this->getPackageSegments();

		$config = $this->laravel['config']['workbench'];

		return new Package($vendor, $name, $config['name'], $config['email']);
	}

	/**
	 * Get the package vendor and name segments from the input.
	 *
	 * @return array
	 */
	protected function getPackageSegments()
	{
		$package = $this->argument('package');

		return array_map('studly_case', explode('/', $package, 2));
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('package', InputArgument::REQUIRED, 'The name (vendor/name) of the package.'),
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
			array('resources', null, InputOption::VALUE_NONE, 'Create Laravel specific directories.'),
		);
	}

}
