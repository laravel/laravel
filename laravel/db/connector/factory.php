<?php namespace Laravel\DB\Connector;

class Factory {

	/**
	 * Create a new database connector instance for a given driver.
	 *
	 * @param  string     $driver
	 * @return Connector
	 */
	public static function make($driver)
	{
		switch ($driver)
		{
			case 'sqlite':
				return new SQLite;

			case 'mysql':
				return new MySQL;

			case 'postgres':
				return new Postgres;

			default:
				return new Generic;
		}
	}

}