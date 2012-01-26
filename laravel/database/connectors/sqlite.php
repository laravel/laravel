<?php namespace Laravel\Database\Connectors; use PDO;

class SQLite extends Connector {

	/**
	 * Establish a PDO database connection.
	 *
	 * @param  array  $config
	 * @return PDO
	 */
	public function connect($config)
	{
		$options = $this->options($config);

		// SQLite provides supported for "in-memory" databases, which exist only for the
		// lifetime of the request. Any given in-memory database may only have one PDO
		// connection open to it at a time. Generally, these databases are used for
		// testing and development purposes, not in production scenarios.
		if ($config['database'] == ':memory:')
		{
			return new PDO('sqlite::memory:', null, null, $options);
		}

		// SQLite databases will be created automatically if they do not exist, so we
		// will not check for the existence of the database file before establishing
		// the PDO connection to the database.
		$path = DATABASE_PATH.$config['database'].'.sqlite';

		return new PDO('sqlite:'.$path, null, null, $options);
	}

}
