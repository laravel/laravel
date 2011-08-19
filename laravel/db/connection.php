<?php namespace Laravel\DB;

class Connection {

	/**
	 * The connection name.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * The connection configuration.
	 *
	 * @var array
	 */
	public $config;

	/**
	 * The PDO connection.
	 *
	 * @var PDO
	 */
	public $pdo;

	/**
	 * All of the queries that have been executed on the connection.
	 *
	 * @var array
	 */
	public $queries = array();

	/**
	 * Create a new Connection instance.
	 *
	 * @param  string     $name
	 * @param  object     $config
	 * @param  Connector  $connector
	 * @return void
	 */
	public function __construct($name, $config, $connector)
	{
		$this->name = $name;
		$this->config = $config;
		$this->pdo = $connector->connect($this->config);
	}

	/**
	 * Execute a SQL query against the connection and return the first result.
	 *
	 * @param  string  $sql
	 * @param  array   $bindings
	 * @return object
	 */
	public function first($sql, $bindings = array())
	{
		return (count($results = $this->query($sql, $bindings)) > 0) ? $results[0] : null;
	}

	/**
	 * Execute a SQL query against the connection.
	 *
	 * The method returns the following based on query type:
	 *
	 *     SELECT -> Array of stdClasses
	 *     UPDATE -> Number of rows affected.
	 *     DELETE -> Number of Rows affected.
	 *     ELSE   -> Boolean true / false depending on success.
	 *
	 * @param  string  $sql
	 * @param  array   $bindings
	 * @return array
	 */
	public function query($sql, $bindings = array())
	{
		$this->queries[] = $sql;

		$query = $this->pdo->prepare($sql);

		$result = $query->execute($bindings);

		if (strpos(strtoupper($sql), 'SELECT') === 0)
		{
			return $query->fetchAll(\PDO::FETCH_CLASS, 'stdClass');
		}
		elseif (strpos(strtoupper($sql), 'UPDATE') === 0 or strpos(strtoupper($sql), 'DELETE') === 0)
		{
			return $query->rowCount();
		}

		return $result;
	}

	/**
	 * Begin a fluent query against a table.
	 *
	 * @param  string  $table
	 * @return Query
	 */
	public function table($table)
	{
		return new Query($table, $this);
	}

	/**
	 * Get the keyword identifier wrapper for the connection.
	 *
	 * @return string
	 */
	public function wrapper()
	{
		if (array_key_exists('wrap', $this->config) and $this->config['wrap'] === false) return '';

		return ($this->driver() == 'mysql') ? '`' : '"';
	}

	/**
	 * Get the driver name for the database connection.
	 *
	 * @return string
	 */
	public function driver()
	{
		return $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
	}

	/**
	 * Get the table prefix for the database connection.
	 *
	 * @return string
	 */
	public function prefix()
	{
		return (array_key_exists('prefix', $this->config)) ? $this->config['prefix'] : '';
	}

}