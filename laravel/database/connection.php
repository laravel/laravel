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
	 * <code>
	 *		// Get the number of rows in the "users" table
	 *		$count = DB::connection()->scalar('select count(*) from users');
	 *
	 *		// Get the sum of payments from the "bank" table
	 *		$sum = DB::connection()->scalar('select sum(payment) from banks where bank_id = ?', array(1));
	 * </code>
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
	 * <code>
	 *		// Get the first result from the "users" table
	 *		$user = DB::connection()->first('select * from users limit 1');
	 *
	 *		// Get the first result from a specified group of users
	 *		$user = DB::connection()->first('select * from users where group_id = ?', array(1));
	 * </code>
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
	 * <code>
	 *		// Execute a query against the connection
	 *		$users = DB::connection()->query('select * from users');
	 *
	 *		// Execute a query against the connection using bindings
	 *		$users = DB::connection()->query('select * from users where group_id = ?', array(1));
	 * </code>
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
	 * <code>
	 *		// Begin a fluent query against the "users" table
	 *		$query = DB::connection()->table('users');
	 *
	 *		// Retrieve an entire table using a fluent query
	 *		$users = DB::connection()->table('users')->get();
	 * </code>
	 *
	 * @param  string       $table
	 * @return Query\Query
	 */
	public function table($table)
	{
		switch ($this->driver())
		{
			case 'pgsql':
				return new Queries\Postgres($this, $this->grammar(), $table);

			default:
				return new Queries\Query($this, $this->grammar(), $table);
		}
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
				return new Queries\Grammars\MySQL;

			case 'pgsql':
				return new Queries\Grammars\Postgres;

			default:
				return new Queries\Grammars\Grammar;
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
	 *
	 * <code>
	 *		// Begin a query against the "users" table
	 *		$query = DB::connection()->users();
	 * </code>
	 */
	public function __call($method, $parameters)
	{
		return $this->table($method);
	}

}