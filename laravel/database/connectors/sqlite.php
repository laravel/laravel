<?php namespace Laravel\Database\Connectors; use PDO;

class SQLite extends Connector {

	/**
	 * Establish a PDO database connection for a given database configuration.
	 *
	 * @param  array  $config
	 * @return PDO
	 */
	public function connect($config)
	{
		$options = $this->options($config);

		// SQLite in-memory databases only live for the lifetime of the current request, and it
		// is impossible to create two connections to the same in-memory database.
		if ($config['database'] == ':memory:')
		{
			return new PDO('sqlite::memory:', null, null, $options);
		}

		// We will check for the database in the default storage directory for the application.
		// Typically, this directory holds all of the SQLite databases for the application.
		elseif (file_exists($path = DATABASE_PATH.$config['database'].'.sqlite'))
		{
			return new PDO('sqlite:'.$path, null, null, $options);
		}

		// If we still haven't located the database, we will assume the given database name
		// is a fully qualified path to the database on disk and attempt to load it.
		elseif (file_exists($config['database']))
		{
			return new PDO('sqlite:'.$config['database'], null, null, $options);
		}

		throw new \Exception("SQLite database [{$config['database']}] could not be found.");
	}

}