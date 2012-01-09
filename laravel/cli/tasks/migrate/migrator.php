<?php namespace Laravel\CLI\Tasks\Migrate;

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

		foreach ($migrations as $migration)
		{
			$migration->up();

			// After running a migration, we log its execution in the
			// migration table so that we can easily determine which
			// migrations we will need to reverse on a rollback.
			$this->database->log($migration['bundle'], $migration['name']);
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

		if (count($migrations) == 0) return false;

		// The "last" method on the resolver returns an array of migrations,
		// along with their bundles and names. We will iterate through each
		// migration and run the "down" method, removing them from the
		// database as we go.
		foreach ($migrations as $migration)
		{
			$migration['migration']->down();

			// By only removing the migration after it has successfully rolled back,
			// we can re-run the rollback command in the event of any errors with
			// the migration. When we re-run, only the migrations that have not
			// been rolled-back for the batch will still be in the database.
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
	}

}