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
		die(var_dump($arguments));
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

			// We'll store a UNIX timestamp indicating the time that
			// the migration command was run. This will allow us to
			// easily rollback migration commands.
			$table->integer('ran_at');

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