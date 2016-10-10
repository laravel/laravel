<?php namespace Illuminate\Database\Migrations;

abstract class Migration {

	/**
	 * The name of the database connection to use.
	 *
	 * @var string
	 */
	protected $connection;

	/**
	 * Get the migration connection name.
	 *
	 * @return string
	 */
	public function getConnection()
	{
		return $this->connection;
	}

}
