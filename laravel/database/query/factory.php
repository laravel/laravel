<?php namespace Laravel\Database\Query;

use Laravel\Database\Query;
use Laravel\Database\Connection;

class Factory {

	/**
	 * Create a new query instance based on the connection driver.
	 *
	 * @param  Connection  $connection
	 * @param  Compiler    $compiler
	 * @param  string      $table
	 * @return Query
	 */
	public function make(Connection $connection, Compiler $compiler, $table)
	{
		switch ($connection->driver())
		{
			case 'pgsql':
				return new Postgres($connection, $compiler, $table);

			default:
				return new Query($connection, $compiler, $table);
		}
	}

}