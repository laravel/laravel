<?php namespace Laravel\Database\Connectors; use PDO;

class SQLite extends Connector {

	/**
	 * The path to the SQLite databases for the application.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Create a new SQLite database connector instance.
	 *
	 * @param  string  $path
	 * @return void
	 */
	public function __construct($path)
	{
		$this->path = $path;
	}

	/**
	 * Establish a PDO database connection for a given database configuration.
	 *
	 * @param  array  $config
	 * @return PDO
	 */
	public function connect($config)
	{
		$options = $this->options($config);

		// SQLite provides supported for "in-memory" databases, which exist only for the
		// lifetime of the request. Any given in-memory database may only have one PDO
		// connection open to it at a time. Generally, these databases are use for
		// testing and development purposes, not in production scenarios.
		if ($config['database'] == ':memory:')
		{
			return new PDO('sqlite::memory:', null, null, $options);
		}

		// First, we will check for the database in the default storage directory for the
		// application. If we don't find the database there, we will assume the database
		// name is actually a full qualified path to the database on disk and attempt
		// to load it. If we still can't find it, we'll bail out.
		elseif (file_exists($path = $this->path.$config['database'].'.sqlite'))
		{
			return new PDO('sqlite:'.$path, null, null, $options);
		}
		elseif (file_exists($config['database']))
		{
			return new PDO('sqlite:'.$config['database'], null, null, $options);
		}

		throw new \OutOfBoundsException("SQLite database [{$config['database']}] could not be found.");
	}

}
