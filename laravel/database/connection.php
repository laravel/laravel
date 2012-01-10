<?php namespace Laravel\Database; use PDO, PDOStatement;

class Connection {

	/**
	 * The raw PDO connection instance.
	 *
	 * @var PDO
	 */
	public $pdo;

	/**
	 * The connection configuration array.
	 *
	 * @var array
	 */
	protected $config;

	/**
	 * The query grammar instance for the connection.
	 *
	 * @var Grammars\Grammar
	 */
	protected $grammar;

	/**
	 * All of the queries that have been executed on all connections.
	 *
	 * @var array
	 */
	public static $queries = array();

	/**
	 * Create a new database connection instance.
	 *
	 * @param  PDO    $pdo
	 * @param  array  $config
	 * @return void
	 */
	public function __construct(PDO $pdo, $config)
	{
		$this->pdo = $pdo;
		$this->config = $config;
	}

	/**
	 * Begin a fluent query against a table.
	 *
	 * <code>
	 *		// Start a fluent query against the "users" table
	 *		$query = DB::connection()->table('users');
	 *
	 *		// Start a fluent query against the "users" table and get all the users
	 *		$users = DB::connection()->table('users')->get();
	 * </code>
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
	 * @return Query\Grammars\Grammar
	 */
	protected function grammar()
	{
		if (isset($this->grammar)) return $this->grammar;

		switch (isset($this->config['grammar']) ? $this->config['grammar'] : $this->driver())
		{
			case 'mysql':
				return $this->grammar = new Query\Grammars\MySQL;

			case 'sqlsrv':
				return $this->grammar = new Query\Grammars\SQLServer;

			default:
				return $this->grammar = new Query\Grammars\Grammar;
		}
	}

	/**
	 * Execute a SQL query against the connection and return a single column result.
	 *
	 * <code>
	 *		// Get the total number of rows on a table
	 *		$count = DB::connection()->only('select count(*) from users');
	 *
	 *		// Get the sum of payment amounts from a table
	 *		$sum = DB::connection()->only('select sum(amount) from payments')
	 * </code>
	 *
	 * @param  string  $sql
	 * @param  array   $bindings
	 * @return mixed
	 */
	public function only($sql, $bindings = array())
	{
		$results = (array) $this->first($sql, $bindings);

		return reset($results);
	}

	/**
	 * Execute a SQL query against the connection and return the first result.
	 *
	 * <code>
	 *		// Execute a query against the database connection
	 *		$user = DB::connection()->first('select * from users');
	 *
	 *		// Execute a query with bound parameters
	 *		$user = DB::connection()->first('select * from users where id = ?', array($id));
	 * </code>
	 *
	 * @param  string  $sql
	 * @param  array   $bindings
	 * @return object
	 */
	public function first($sql, $bindings = array())
	{
		if (count($results = $this->query($sql, $bindings)) > 0)
		{
			return $results[0];
		}
	}

	/**
	 * Execute a SQL query and return an array of StdClass objects.
	 *
	 * @param  string  $sql
	 * @param  array   $bindings
	 * @return array
	 */
	public function query($sql, $bindings = array())
	{
		list($statement, $result) = $this->execute($sql, $bindings);

		return $statement->fetchAll(PDO::FETCH_CLASS, 'stdClass');
	}

	/**
	 * Execute a SQL UPDATE query and return the affected row count.
	 *
	 * @param  string  $sql
	 * @param  array   $bindings
	 * @return int
	 */
	public function update($sql, $bindings = array())
	{
		list($statement, $result) = $this->execute($sql, $bindings);

		return $statement->rowCount();
	}

	/**
	 * Execute a SQL DELETE query and return the affected row count.
	 *
	 * @param  string  $sql
	 * @param  array   $bindings
	 * @return int
	 */
	public function delete($sql, $bindings = array())
	{
		list($statement, $result) = $this->execute($sql, $bindings);

		return $statement->rowCount();
	}

	/**
	 * Execute an SQL query and return the boolean result of the PDO statement.
	 *
	 * @param  string  $sql
	 * @param  array   $bindings
	 * @return bool
	 */
	public function statement($sql, $bindings = array())
	{
		list($statement, $result) = $this->execute($sql, $bindings);

		return $result;
	}

	/**
	 * Execute a SQL query against the connection.
	 *
	 * The PDO statement and boolean result will be return in an array.
	 *
	 * @param  string  $sql
	 * @param  array   $bindings
	 * @return array
	 */
	protected function execute($sql, $bindings = array())
	{
		// Since expressions are injected into the query as strings, we need to
		// remove them from the array of bindings. After we have removed them,
		// we'll reset the array so there aren't gaps in the keys.
		$bindings = array_values(array_filter($bindings, function($binding)
		{
			return ! $binding instanceof Expression;
		}));

		$sql = $this->transform($sql, $bindings);

		$statement = $this->pdo->prepare($sql);

		// Every query is timed so that we can log the executinon time along
		// with the query SQL and array of bindings. This should be make it
		// convenient for the developer to profile the application's query
		// performance to diagnose bottlenecks.
		$time = microtime(true);

		$result = $statement->execute($bindings);

		$time = number_format((microtime(true) - $time) * 1000, 2);

		// Once we have execute the query, we log the SQL, bindings, and
		// execution time in a static array that is accessed by all of
		// the connections used by the application. This allows us to
		// review all of the executed SQL.
		static::$queries[] = compact('sql', 'bindings', 'time');

		return array($statement, $result);
	}

	/**
	 * Transform an SQL query into an executable query.
	 *
	 * @param  string  $sql
	 * @param  array   $bindings
	 * @return string
	 */
	protected function transform($sql, $bindings)
	{
		// Laravel provides an easy short-cut notation for writing raw
		// WHERE IN statements. If (...) is in the query, it will be
		// replaced with the correct number of parameters based on
		// the bindings for the query.
		if (strpos($sql, '(...)') !== false)
		{
			for ($i = 0; $i < count($bindings); $i++)
			{
				// If the binding is an array, we can assume it is being used
				// to fill a "where in" condition, so we'll replace the next
				// place-holder in the SQL query with the correct number of
				// parameters based on the elements in the binding.
				if (is_array($bindings[$i]))
				{
					$parameters = implode(', ', array_fill(0, count($bindings[$i]), '?'));

					$sql = preg_replace('~\(\.\.\.\)~', "({$parameters})", $sql, 1);
				}
			}			
		}

		return trim($sql);
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