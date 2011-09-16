<?php namespace Laravel\Database;

use PDO;
use PDOStatement;

class Connection {

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
	 * Create a new database connection instance.
	 *
	 * @param  PDO   $pdo
	 * @return void
	 */
	public function __construct(PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	/**
	 * Execute a SQL query against the connection and return a scalar result.
	 *
	 * @param  string     $sql
	 * @param  array      $bindings
	 * @return int|float
	 */
	public function scalar($sql, $bindings = array())
	{
		$result = (array) $this->first($sql, $bindings);

		return (strpos(strtolower(trim($sql)), 'select count') === 0) ? (int) reset($result) : (float) reset($result);
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
	protected function execute(PDOStatement $statement, $bindings)
	{
		$result = $statement->execute($bindings);

		if (strpos(strtoupper($statement->queryString), 'SELECT') === 0)
		{
			return $statement->fetchAll(PDO::FETCH_CLASS, 'stdClass');
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
		return new Query($this, $this->grammar(), $table);
	}

	/**
	 * Create a new query grammar for the connection.
	 *
	 * @return Queries\Grammars\Grammar
	 */
	protected function grammar()
	{
		switch ($this->driver())
		{
			case 'mysql':
				return new Grammars\MySQL;

			case 'pgsql':
				return new Grammars\Postgres;

			default:
				return new Grammars\Grammar;
		}
	}

	/**
	 * Get the driver name for the database connection.
	 *
	 * @return string
	 */
	public function driver()
	{
		return $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
	}

	/**
	 * Magic Method for dynamically beginning queries on database tables.
	 */
	public function __call($method, $parameters)
	{
		return $this->table($method);
	}

}