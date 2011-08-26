<?php namespace Laravel\Database\Query\Compiler;

use Laravel\Database\Connection;
use Laravel\Database\Query\Compiler;

class Factory {

	/**
	 * Create a new query compiler for a given connection.
	 *
	 * Using driver-based compilers allows us to provide the proper syntax to different database
	 * systems using a common API. A core set of functions is provided through the base Compiler
	 * class, which can be extended and overridden for various database systems.
	 *
	 * @param  Connection  $connection
	 * @return Compiler
	 */
	public static function make(Connection $connection)
	{
		$compiler = (isset($connection->config['compiler'])) ? $connection->config['compiler'] : $connection->driver();

		switch ($compiler)
		{
			case 'mysql':
				return new MySQL;

			case 'pgsql':
				return new Postgres;

			default:
				return new Compiler;
		}
	}

}