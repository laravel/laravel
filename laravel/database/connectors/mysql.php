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

		// Format the initial MySQL PDO connection string. These options are required
		// for every MySQL connection that is established. The connection strings
		// have the following convention: "mysql:host=hostname;dbname=database"
		$dsn = "mysql:host={$host};dbname={$database}";

		// Check for any optional MySQL PDO options. These options are not required
		// to establish a PDO connection; however, may be needed in certain server
		// or hosting environments used by the developer.
		foreach (array('port', 'unix_socket') as $key => $value)
		{
			if (isset($config[$key]))
			{
				$dsn .= ";{$key}={$value}";
			}
		}

		$connection = new PDO($dsn, $username, $password, $this->options($config));

		if (isset($config['charset']))
		{
			$connection->prepare("SET NAMES '{$charset}'")->execute();
		}

		return $connection;
	}

}