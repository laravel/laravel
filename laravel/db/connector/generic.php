<?php namespace Laravel\DB\Connector;

use Laravel\DB\Connector;

class Generic extends Connector {

	/**
	 * Establish a PDO database connection.
	 *
	 * @param  array  $config
	 * @return PDO
	 */
	public function connect($config)
	{
		return new \PDO($config['driver'].':'.$config['dsn'], $config['username'], $config['password'], $this->options);
	}

}