<?php namespace Laravel\Database\Query;

use Laravel\Database\Query;
use Laravel\Database\Connection;

class Factory {

	/**
	 * Create a new query instance based on the connection driver.
	 *
	 * @param  string      $table
	 * @param  Connection  $connection
	 * @param  Compiler    $compiler
	 * @return Query
	 */
	public static function make($table, Connection $connection, Compiler $compiler)
	{
		switch ($connection->driver())
		{
			case 'pgsql':
				return new Postgres($table, $connection, $compiler);

			default:
				return new Query($table, $connection, $compiler);
		}
	}

}