<?php namespace Laravel\CLI\Tasks\Migrate;

use Laravel\Bundle;
use Laravel\Database as DB;

class Resolver {

	/**
	 * The CLI options.
	 *
	 * @var array
	 */
	protected $options;

	/**
	 * Create a new migration resolver instance.
	 *
	 * @param  array  $options
	 * @return void
	 */
	public function __construct($options)
	{
		$this->options = $options;
	}

	/**
	 * Resolve all of the outstanding migrations.
	 *
	 * @param  string  $bundle
	 * @return array
	 */
	public function outstanding($bundle = null)
	{
		die(var_dump($bundle));
	}

	/**
	 * Resolve an array of the last batch of migrations.
	 *
	 * @return array
	 */
	public function last()
	{
		$table = $this->table();

		// First we need to grab the last batch ID from the migration table,
		// as this will allow us to grab the lastest batch of migrations
		// that need to be run for a rollback command.
		$id = $table->max('batch');

		// Once we have the batch ID, we will pull all of the rows for that
		// batch. Then we can feed the results into the resolve method to
		// get the migration instances for the command.
		$migrations = $table->where_batch($id)->order_by('name', 'desc');

		return $this->resolve($migrations->get());
	}

	/**
	 * Resolve an array of migration instances.
	 *
	 * @param  array  $migrations
	 * @return array
	 */
	protected function resolve($migrations)
	{
		$instances = array();

		foreach ($migrations as $migration)
		{
			$migration = (array) $migration;

			// The migration array contains the bundle name, so we will get the
			// path to the bundle's migrations and resolve an instance of the
			// migration using the name.
			$path = Bundle::path($migration['bundle']).'migrations/';

			// Migrations are not resolved through the auto-loader, so we will
			// manually instantiate the migration class instances for each of
			// the migration names we're given.
			$name = $migration['name'];

			require_once $path.$name.EXT;

			// Since the migration name will begin with the numeric ID, we'll
			// slice off the ID so we are left with the migration class name.
			// The IDs are for sorting when resolving outstanding migrations.
			$class = substr($name, strpos($name, '_') + 1);

			$instances = new $class;
		}

		return $instances;
	}

	/**
	 * Get a database query instance for the migration table.
	 *
	 * @return Query
	 */
	protected function table()
	{
		$connection = DB::connection(array_get($this->options, 'db'));

		return $connection->table('laravel_migrations');
	}

}