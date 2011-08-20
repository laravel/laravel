<?php namespace Laravel\DB\Connector;

use Laravel\DB\Connector;

class Callback extends Connector {

	/**
	 * Establish a PDO database connection.
	 *
	 * @param  array  $config
	 * @return PDO
	 */
	public function connect($config)
	{
		return call_user_func($config['connector']);
	}

}