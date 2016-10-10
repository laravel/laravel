<?php namespace Illuminate\Database\Console\Migrations;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Database\Migrations\MigrationCreator;

class MigrateMakeCommand extends BaseCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'migrate:make';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new migration file';

	/**
	 * The migration creator instance.
	 *
	 * @var \Illuminate\Database\Migrations\MigrationCreator
	 */
	protected $creator;

	/**
	 * The path to the packages directory (vendor).
	 *
	 * @var string
	 */
	protected $packagePath;

	/**
	 * Create a new migration install command instance.
	 *
	 * @param  \Illuminate\Database\Migrations\MigrationCreator  $creator
	 * @param  string  $packagePath
	 * @return void
	 */
	public function __construct(MigrationCreator $creator, $packagePath)
	{
		parent::__construct();

		$this->creator = $creator;
		$this->packagePath = $packagePath;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		// It's possible for the developer to specify the tables to modify in this
		// schema operation. The developer may also specify if this table needs
		// to be freshly created so we can create the appropriate migrations.
		$name = $this->input->getArgument('name');

		$table = $this->input->getOption('table');

		$create = $this->input->getOption('create');

		if ( ! $table && is_string($create)) $table = $create;

		// Now we are ready to write the migration out to disk. Once we've written
		// the migration out, we will dump-autoload for the entire framework to
		// make sure that the migrations are registered by the class loaders.
		$this->writeMigration($name, $table, $create);

		$this->call('dump-autoload');
	}

	/**
	 * Write the migration file to disk.
	 *
	 * @param  string  $name
	 * @param  string  $table
	 * @param  bool    $create
	 * @return string
	 */
	protected function writeMigration($name, $table, $create)
	{
		$path = $this->getMigrationPath();

		$file = pathinfo($this->creator->create($name, $path, $table, $create), PATHINFO_FILENAME);

		$this->line("<info>Created Migration:</info> $file");
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('name', InputArgument::REQUIRED, 'The name of the migration'),
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
			array('bench', null, InputOption::VALUE_OPTIONAL, 'The workbench the migration belongs to.', null),

			array('create', null, InputOption::VALUE_OPTIONAL, 'The table to be created.'),

			array('package', null, InputOption::VALUE_OPTIONAL, 'The package the migration belongs to.', null),

			array('path', null, InputOption::VALUE_OPTIONAL, 'Where to store the migration.', null),

			array('table', null, InputOption::VALUE_OPTIONAL, 'The table to migrate.'),
		);
	}

}
