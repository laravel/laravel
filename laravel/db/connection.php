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
	 * The database connector instance.
	 *
	 * @var Connector
	 */
	private $connector;

	/**
	 * Create a new Connection instance.
	 *
	 * @param  string     $name
	 * @param  array      $config
	 * @param  Connector  $connector
	 * @return void
	 */
	public function __construct($name, $config, Connector $connector)
	{
		$this->name = $name;
		$this->config = $config;
		$this->connector = $connector;
	}

	/**
	 * Establish the PDO connection for the connection instance.
	 *
	 * @return void
	 */
	public function connect()
	{
		$this->pdo = $this->connector->connect($this->config);
	}

	/**
	 * Determine if a PDO connection has been established for the connection.
	 *
	 * @return bool
	 */
	public function connected()
	{
		return ! is_null($this->pdo);
	}

	/**
	 * Execute a SQL query against the connection and return a scalar result.
	 *
	 * @param  string  $sql
	 * @param  array   $bindings
	 * @return mixed
	 */
	public function scalar($sql, $bindings = array())
	{
		$result = (array) $this->first($sql, $bindings);

		return (strpos(strtolower($sql), 'select count') === 0) ? (int) reset($result) : (float) reset($result);
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
	 * @return mixed
	 */
	public function query($sql, $bindings = array())
	{
		if ( ! $this->connected()) $this->connect();

		$this->queries[] = compact('sql', 'bindings');

		return $this->execute($this->pdo->prepare(trim($sql)), $bindings);
	}

	/**
	 * Execute a prepared PDO statement and return the appropriate results.
	 *
	 * @param  PDOStatement  $statement
	 * @param  array         $results
	 * @return mixed
	 */
	private function execute(\PDOStatement $statement, $bindings)
	{
		$result = $statement->execute($bindings);

		if (strpos(strtoupper($statement->queryString), 'SELECT') === 0)
		{
			return $statement->fetchAll(\PDO::FETCH_CLASS, 'stdClass');
		}
		elseif (strpos(strtoupper($statement->queryString), 'INSERT') === 0)
		{
			return $result;
		}

		return $statement->rowCount();
	}

	/**
	 * Begin a fluent query against a table.
	 *
	 * @param  string  $table
	 * @return Query
	 */
	public function table($table)
	{
		return Query\Factory::make($table, $this, Query\Compiler\Factory::make($this));
	}

	/**
	 * Get the driver name for the database connection.
	 *
	 * @return string
	 */
	public function driver()
	{
		if ( ! $this->connected()) $this->connect();

		return $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
	}

}