<?php namespace Laravel\CLI\Tasks\Migrate;

use Laravel\Bundle;
use Laravel\Database\Schema;

class Migrator {

	/**
	 * The migration resolver.
	 *
	 * @var Resolver
	 */
	protected $resolver;

	/**
	 * Create a new instance of the Migrator CLI task.
	 *
	 * @param  Resolver  $resolver
	 * @return void
	 */
	public function __construct(Resolver $resolver)
	{
		$this->resolver = $resolver;
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
			list($bundle, $version) = $this->parse($arguments[0]);
			
			$this->migrate($bundle, $version);
		}
	}

	/**
	 * Run the outstanding migrations for a given bundle.
	 *
	 * @param  string  $bundle
	 * @param  int     $version
	 * @return void
	 */
	protected function migrate($bundle = null, $version = null)
	{
		$migrations = $this->resolver->resolve($bundle, $version);

		foreach ($migrations as $name => $migration)
		{
			$migration->up();

			// After running a migration, we log it's execution in the
			// migration table so that we can easily determine which
			// migrations we will need to reverse on a rollback.
			$this->database->log($bundle, $name);
		}
	}

	/**
	 * Rollback the latest migration command.
	 *
	 * @param  array  $arguments
	 * @return void
	 */
	public function rollback($arguments = array())
	{
		$migrations = $this->resolver->last($this->database);

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

	/**
	 * Parse the migration identifier.
	 *
	 * @param  string  $identifier
	 * @return array
	 */
	protected function parse($identifier)
	{
		$segments = Bundle::parse($identifier);

		// If the identifier has two segments, it includes both a
		// bundle and a version number, so we will return both of
		// them. If there is only one segment, only a version has
		// been specified, and we will run the migration for the
		// default bundle. When there are no segments, we will
		// run all outstanding migrations for all bundles.
		$count = count($segments);

		switch ($count)
		{
			case 2:
				return $segments;

			case 1:
				return array(DEFAULT_BUNDLE, $segments[0]);

			default:
				return array(DEFAULT_BUNDLE, null);
		}
	}

}