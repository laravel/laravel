<?php namespace Laravel\CLI\Tasks\Migrate;

class Resolver {

	/**
	 * The migration database instance.
	 *
	 * @var Database
	 */
	protected $database;

	/**
	 * Create a new migration resolver instance.
	 *
	 * @param  Database  $database
	 * @return void
	 */
	public function __construct(Database $database)
	{
		$this->database = $database;
	}

	/**
	 * Resolve all of the outstanding migrations.
	 *
	 * @return array
	 */
	public function resolve($bundle)
	{
		
	}

	/**
	 * Resolve an instance of the last run migration.
	 *
	 * @param  Database  $database
	 * @return void
	 */
	public function last(Database $database)
	{
		//
	}

}