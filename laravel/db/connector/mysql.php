<?php namespace Laravel\DB\Connector;

use Laravel\DB\Connector;

class MySQL extends Connector {

	/**
	 * Establish a PDO database connection.
	 *
	 * @param  array  $config
	 * @return PDO
	 */
	public function connect($config)
	{
		$dsn = $config['driver'].':host='.$config['host'].';dbname='.$config['database'];

		if (isset($config['port']))
		{
			$dsn .= ';port='.$config['port'];
		}

		$connection = new \PDO($dsn, $config['username'], $config['password'], $this->options);

		if (isset($config['charset']))
		{
			$connection->prepare("SET NAMES '".$config['charset']."'")->execute();
		}

		return $connection;
	}

}