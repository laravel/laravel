<?php namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Foundation\Composer;
use Symfony\Component\Finder\Finder;

class AutoloadCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'dump-autoload';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = "Regenerate framework autoload files";

	/**
	 * The composer instance.
	 *
	 * @var \Illuminate\Foundation\Composer
	 */
	protected $composer;

	/**
	 * Create a new optimize command instance.
	 *
	 * @param  \Illuminate\Foundation\Composer  $composer
	 * @return void
	 */
	public function __construct(Composer $composer)
	{
		parent::__construct();

		$this->composer = $composer;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->call('optimize');

		foreach ($this->findWorkbenches() as $workbench)
		{
			$this->comment("Running for workbench [{$workbench['name']}]...");

			$this->composer->setWorkingPath($workbench['path'])->dumpOptimized();
		}
	}

	/**
	 * Get all of the workbench directories.
	 *
	 * @return array
	 */
	protected function findWorkbenches()
	{
		$results = array();

		foreach ($this->getWorkbenchComposers() as $file)
		{
			$results[] = array('name' => $file->getRelativePath(), 'path' => $file->getPath());
		}

		return $results;
	}

	/**
	 * Get all of the workbench composer files.
	 *
	 * @return \Symfony\Component\Finder\Finder
	 */
	protected function getWorkbenchComposers()
	{
		$workbench = $this->laravel['path.base'].'/workbench';

		if ( ! is_dir($workbench)) return array();

		return Finder::create()->files()->in($workbench)->name('composer.json')->depth('< 3');
	}

}
