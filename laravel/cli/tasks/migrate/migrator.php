<?php namespace Laravel\CLI\Tasks\Migrate;

use Laravel\Str;
use Laravel\File;
use Laravel\Bundle;
use Laravel\CLI\Tasks\Task;
use Laravel\Database\Schema;

class Migrator extends Task {

	/**
	 * The migration resolver instance.
	 *
	 * @var Resolver
	 */
	protected $resolver;

	/**
	 * The migration database instance.
	 *
	 * @var Database
	 */
	protected $database;

	/**
	 * Create a new instance of the Migrator CLI task.
	 *
	 * @param  Resolver  $resolver
	 * @param  Database  $database
	 * @return void
	 */
	public function __construct(Resolver $resolver, Database $database)
	{
		$this->resolver = $resolver;
		$this->database = $database;
	}

	/**
	 * Run a database migration command.
	 *
	 * @param  array  $arguments
	 * @return void
	 */
	public function run($arguments = array())
	{
		// If no arguments were passed to the task, we will just migrate
		// to the latest version across all bundles. Otherwise, we will
		// parse the arguments to determine the bundle for which the
		// database migrations should be run.
		if (count($arguments) == 0)
		{
			$this->migrate();
		}
		else
		{
			$this->migrate(array_get($arguments, 0));
		}
	}

	/**
	 * Run the outstanding migrations for a given bundle.
	 *
	 * @param  string  $bundle
	 * @param  int     $version
	 * @return void
	 */
	public function migrate($bundle = null, $version = null)
	{
		$migrations = $this->resolver->outstanding($bundle);

		if (count($migrations) == 0)
		{
			echo "No outstanding migrations.";

			return;
		}

		// We need to grab the latest batch ID and increment it
		// by one. This allows us to group the migrations such
		// that we can easily determine which migrations need
		// to roll back for the command.
		$batch = $this->database->batch() + 1;

		foreach ($migrations as $migration)
		{
			$migration['migration']->up();

			echo 'Migrated: '.$this->display($migration).PHP_EOL;

			// After running a migration, we log its execution in the
			// migration table so that we can easily determine which
			// migrations we'll reverse on a rollback.
			$this->database->log($migration['bundle'], $migration['name'], $batch);
		}
	}

	/**
	 * Rollback the latest migration command.
	 *
	 * @param  array  $arguments
	 * @return bool
	 */
	public function rollback($arguments = array())
	{
		$migrations = $this->resolver->last();

		if (count($migrations) == 0)
		{
			echo "Nothing to rollback.";

			return false;
		}

		// The "last" method on the resolver returns an array of migrations,
		// along with their bundles and names. We will iterate through each
		// migration and run the "down" method, removing them from the
		// database as we go.
		foreach (array_reverse($migrations) as $migration)
		{
			$migration['migration']->down();

			echo 'Rolled back: '.$this->display($migration).PHP_EOL;

			// By only removing the migration after it has successfully rolled back,
			// we can re-run the rollback command in the event of any errors with
			// the migration. When we re-run, only the migrations that have not
			// been rolled back will still be in the database.
			$this->database->delete($migration['bundle'], $migration['name']);
		}

		return true;
	}

	/**
	 * Rollback all of the executed migrations.
	 *
	 * @param  array  $arguments
	 * @return void
	 */
	public function reset($arguments = array())
	{
		while ($this->rollback()) {};
	}

	/**
	 * Install the database tables used by the migration system.
	 *
	 * @return void
	 */
	public function install()
	{
		Schema::table('laravel_migrations', function($table)
		{
			$table->create();

			// Migrations can be run for a specific bundle, so we'll use
			// the bundle name and string migration name as an unique ID
			// for the migrations, allowing us to easily identify which
			// migrations have been run for each bundle.
			$table->string('bundle');

			$table->string('name');

			// When running a migration command, we will store a batch
			// ID with each of the rows on the table. This will allow
			// us to grab all of the migrations that were run for the
			// last command when performing rollbacks.
			$table->integer('batch');

			$table->primary(array('bundle', 'name'));
		});

		echo "Migration table created successfully.";
	}

	/**
	 * Generate a new migration file.
	 *
	 * @param  array   $arguments
	 * @return string
	 */
	public function make($arguments = array())
	{
		if (count($arguments) == 0)
		{
			throw new \Exception("I need to know what to name the migration.");
		}

		list($bundle, $migration) = Bundle::parse($arguments[0]);

		// The migration path is prefixed with the date timestamp, which
		// is a better way of ordering migrations than a simple integer
		// incrementation, since developers may start working on the
		// next migration at the same time unknowingly.
		$prefix = date('Y_m_d_His');

		$path = Bundle::path($bundle).'migrations'.DS;

		// If the migration directory does not exist for the bundle,
		// we will create the directory so there aren't errors when
		// when we try to write the migration file.
		if ( ! is_dir($path)) mkdir($path);

		$file = $path.$prefix.'_'.$migration.EXT;

		File::put($file, $this->stub($bundle, $migration));

		echo "Great! New migration created!";

		// Once the migration has been created, we'll return the
		// migration file name so it can be used by the task
		// consumer if necessary.
		return $file;
	}

	/**
	 * Get the stub migration with the proper class name.
	 *
	 * @param  string  $bundle
	 * @param  string  $migration
	 * @return string
	 */
	protected function stub($bundle, $migration)
	{
		$stub = File::get(path('sys').'cli/tasks/migrate/stub'.EXT);

		$prefix = Bundle::class_prefix($bundle);

		// The class name is formatted simialrly to tasks and controllers,
		// where the bundle name is prefixed to the class if it is not in
		// the default "application" bundle.
		$class = $prefix.Str::classify($migration);

		return str_replace('{{class}}', $class, $stub);
	}

	/**
	 * Get the migration bundle and name for display.
	 *
	 * @param  array   $migration
	 * @return string
	 */
	protected function display($migration)
	{
		return $migration['bundle'].'/'.$migration['name'];
	}

}