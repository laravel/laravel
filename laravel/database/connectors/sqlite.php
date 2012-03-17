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

		// SQLite provides supported for "in-memory" databases, which exist only for
		// lifetime of the request. Any given in-memory database may only have one
		// PDO connection open to it at a time. These are mainly for tests.
		if ($config['database'] == ':memory:')
		{
			return new PDO('sqlite::memory:', null, null, $options);
		}

		// We'll allow the "database" configuration option to be a fully qualified
		// path to the database so we'll check if that is the case first. If it
		// isn't a fully qualified path we will use the storage directory.
		if (file_exists($config['database']))
		{
			$path = $config['database'];
		}

		// The database option does not appear to be a fully qualified path so we
		// will just assume it is a relative path from the storage directory
		// which is typically used to store all SQLite databases.
		else
		{
			$path = path('storage').'database'.DS.$config['database'].'.sqlite';
		}

		return new PDO('sqlite:'.$path, null, null, $options);
	}

}
