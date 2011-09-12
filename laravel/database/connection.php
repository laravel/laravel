<?php namespace Laravel\Database;

use PDO;
use PDOStatement;

class Connection {

	/**
	 * The connection name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The connection configuration.
	 *
	 * @var array
	 */
	protected $config;

	/**
	 * The database connector instance.
	 *
	 * @var Connector\Connector
	 */
	protected $connector;

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
	 * @param  Connector\Connector $connector
	 * @param  Query\Factory       $factory
	 * @param  Compiler\Factory    $compiler
	 * @param  string              $name
	 * @param  array               $config
	 * @return void
	 */
	public function __construct(Connector\Connector $connector, Query\Factory $query, Query\Compiler\Factory $compiler, $name, $config)
	{
		$this->name = $name;
		$this->query = $query;
		$this->config = $config;
		$this->compiler = $compiler;
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
	 * </code>
	 *
	 * @param  string  $table
	 * @return Query
	 */
	public function table($table)
	{
		return $this->query->make($this, $this->compiler->make($this), $table);
	}

	/**
	 * Get the driver name for the database connection.
	 *
	 * @return string
	 */
	public function driver()
	{
		if ( ! $this->connected()) $this->connect();

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