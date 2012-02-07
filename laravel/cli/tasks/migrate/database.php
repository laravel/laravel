<?php namespace Laravel\CLI\Tasks\Migrate;

use Laravel\Request;
use Laravel\Database as DB;

class Database {

	/**
	 * Log a migration in the migration table.
	 *
	 * @param  string  $bundle
	 * @param  string  $name
	 * @param  int     $batch
	 * @return void
	 */
	public function log($bundle, $name, $batch)
	{
		$this->table()->insert(compact('bundle', 'name', 'batch'));
	}

	/**
	 * Delete a row from the migration table.
	 *
	 * @param  string  $bundle
	 * @param  string  $name
	 * @return void
	 */
	public function delete($bundle, $name)
	{
		$this->table()->where_bundle_and_name($bundle, $name)->delete();
	}

	/**
	 * Return an array of the last batch of migrations.
	 *
	 * @return array
	 */
	public function last()
	{
		$table = $this->table();

		// First we need to grab the last batch ID from the migration table,
		// as this will allow us to grab the lastest batch of migrations
		// that need to be run for a rollback command.
		$id = $this->batch();

		// Once we have the batch ID, we will pull all of the rows for that
		// batch. Then we can feed the results into the resolve method to
		// get the migration instances for the command.
		return $table->where_batch($id)->order_by('name', 'desc')->get();
	}

	/**
	 * Get all of the migrations that have run for a bundle.
	 *
	 * @param  string  $bundle
	 * @return array
	 */
	public function ran($bundle)
	{
		return $this->table()->where_bundle($bundle)->lists('name');
	}

	/**
	 * Get the maximum batch ID from the migration table.
	 *
	 * @return int
	 */
	public function batch()
	{
		return $this->table()->max('batch');
	}

	/**
	 * Get a database query instance for the migration table.
	 *
	 * @return Laravel\Database\Query
	 */
	protected function table()
	{
		return DB::connection(Request::server('cli.db'))->table('laravel_migrations');
	}

}