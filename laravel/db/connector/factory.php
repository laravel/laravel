<?php namespace Laravel\DB\Connector;

class Factory {

	/**
	 * Create a new database connector instance for a given driver.
	 *
	 * @param  array      $config
	 * @return Connector
	 */
	public static function make($config)
	{
		if (isset($config['connector'])) return new Callback;

		switch ($config['driver'])
		{
			case 'sqlite':
				return new SQLite;

			case 'mysql':
				return new MySQL;

			case 'pgsql':
				return new Postgres;
		}

		throw new \Exception("Database configuration is invalid. Please verify your configuration.");
	}

}