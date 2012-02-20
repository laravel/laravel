<?php namespace Laravel\Database\Connectors; use PDO;

class MySQL extends Connector {

	/**
	 * Establish a PDO database connection.
	 *
	 * @param  array  $config
	 * @return PDO
	 */
	public function connect($config)
	{
		extract($config);

		$dsn = "mysql:host={$host};dbname={$database}";

		// The developer has the freedom of specifying a port for the MySQL database
		// or the default port (3306) will be used to make the connection by PDO.
		// The Unix socket may also be specified if necessary.
		if (isset($config['port']))
		{
			$dsn .= ";port={$config['port']}";
		}

		if (isset($config['unix_socket']))
		{
			$dsn .= ";unix_socket={$config['unix_socket']}";
		}

		$connection = new PDO($dsn, $username, $password, $this->options($config));

		// If a character set has been specified, we'll execute a query against
		// the database to set the correct character set. By default, this is
		// set to UTF-8 which should be fine for most scenarios.
		if (isset($config['charset']))
		{
			$connection->prepare("SET NAMES '{$config['charset']}'")->execute();
		}

		return $connection;
	}

}