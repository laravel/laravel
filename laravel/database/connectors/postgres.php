<?php namespace Laravel\Database\Connectors; use PDO;

class Postgres extends Connector {

	/**
	 * Establish a PDO database connection for a given database configuration.
	 *
	 * @param  array  $config
	 * @return PDO
	 */
	public function connect($config)
	{
		extract($config);

		// Format the initial Postgres PDO connection string. These options are required
		// for every Postgres connection that is established. The connection strings
		// have the following convention: "pgsql:host=hostname;dbname=database"
		$dsn = sprintf('%s:host=%s;dbname=%s', $driver, $host, $database);

		// Check for any optional Postgres PDO options. These options are not required
		// to establish a PDO connection; however, may be needed in certain server
		// or hosting environments used by the developer.
		foreach (array('port') as $key => $value)
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