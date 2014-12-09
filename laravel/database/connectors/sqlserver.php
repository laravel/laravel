<?php namespace Laravel\Database\Connectors; use PDO;

class SQLServer extends Connector {

	/**
	 * The PDO connection options.
	 *
	 * @var array
	 */
	protected $options = array(
			PDO::ATTR_CASE => PDO::CASE_LOWER,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
			PDO::ATTR_STRINGIFY_FETCHES => false,
	);

	/**
	 * Establish a PDO database connection.
	 *
	 * @param  array  $config
	 * @return PDO
	 */
	public function connect($config)
	{
		extract($config);

		// Format the SQL Server connection string. This connection string format can
		// also be used to connect to Azure SQL Server databases. The port is defined
		// directly after the server name, so we'll create that first.
		$port = (isset($port)) ? ','.$port : '';
		
		//check for dblib for mac users connecting to mssql (utilizes freetds)
		if (in_array('dblib',PDO::getAvailableDrivers()))
		{
			$dsn = "dblib:host={$host}{$port};dbname={$database}";
		}
		else
		{
			$dsn = "sqlsrv:Server={$host}{$port};Database={$database}";
		}

		return new PDO($dsn, $username, $password, $this->options($config));
	}

}