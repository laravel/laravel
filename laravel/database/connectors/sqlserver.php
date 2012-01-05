<?php namespace Laravel\Database\Connectors;

class SQLServer extends Connector {

	/**
	 * Establish a PDO database connection for a given database configuration.
	 *
	 * @param  array  $config
	 * @return PDO
	 */
	public function connect($config)
	{
		extract($config);

		// Format the SQL Server connection string. This connection string format can
		// also be used to connect to Azure SQL Server databases. The port is defined
		// directly after the server name, so we'll create that and then create the
		// final DSN string to pass to PDO.
		$port = (isset($port)) ? ','.$port : '';

		$dsn = "sqlsrv:Server={$host}{$port};Database={$database}";

		return new PDO($dsn, $username, $password, $this->options($config));
	}

}