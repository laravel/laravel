<?php namespace Laravel\Database\Connectors;

class Factory {

	/**
	 * Create a new database connector instance.
	 *
	 * @param  string     $driver
	 * @return Connector
	 */
	public static function make($driver)
	{
		switch ($driver)
		{
			case 'sqlite':
				return new SQLite(DATABASE_PATH);

			case 'mysql':
				return new MySQL;

			case 'pgsql':
				return new Postgres;

			default:
				throw new \Exception("Database driver [$driver] is not supported.");
		}
	}

}