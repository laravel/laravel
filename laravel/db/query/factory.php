<?php namespace Laravel\DB\Query;

use Laravel\DB\Query;
use Laravel\DB\Connection;

class Factory {

	/**
	 * Create a new query instance for a given driver.
	 *
	 * @param  string  $table
	 * @param  Connection  $connection
	 * @return Query
	 */
	public static function make($table, Connection $connection)
	{
		switch ($connection->driver())
		{
			case 'postgres':
				return new Postgres($table, $connection);

			default:
				return new Query($table, $connection);
		}
	}

}