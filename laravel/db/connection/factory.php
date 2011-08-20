<?php namespace Laravel\DB\Connection;

use Laravel\DB\Connector;
use Laravel\DB\Connection;

class Factory {

	/**
	 * Get a connnection instance.
	 *
	 * The connection instance created depends on the driver being used.
	 *
	 * @param  string      $connection
	 * @param  object      $config
	 * @param  Connector   $connector
	 * @return Connection
	 */
	public static function make($connection, $config, Connector $connector)
	{
		switch ($config['driver'])
		{
			case 'mysql':
				return new MySQL($connection, $config, $connector);
			
			default:
				return new Connection($connection, $config, $connector);
		}
	}

}