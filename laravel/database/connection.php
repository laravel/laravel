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
		return $results = (array) $this->first($sql, $bindings);

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
	 *		// Execute a query against the database connection
	 *		$users = DB::connection()->query('select * from users');
	 *
	 *		// Execute a query with bound parameters
	 *		$user = DB::connection()->query('select * from users where id = ?', array($id));
	 * </code>
	 *
	 * @param  string  $sql
	 * @param  array   $bindings
	 * @return mixed
	 */
	public function query($sql, $bindings = array())
	{
		// Since expressions are injected into the query as strings, we need to
		// remove them from the array of bindings. After we have removed them,
		// we'll reset the array so there are no gaps in the numeric keys.
		foreach ($bindings as $key => $value)
		{
			if ($value instanceof Expression) unset($bindings[$key]);
		}

		$bindings = array_values($bindings);

		$sql = $this->transform($sql, $bindings);

		return $this->execute($this->pdo->prepare($sql), $bindings);
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
		// Laravel provides an easy short-cut for writing raw WHERE IN statements.
		// If "(...)" is in the query, it will be replaced with the correct number
		// of parameters based on the bindings for the query.
		if (strpos($sql, '(...)') !== false)
		{
			for ($i = 0; $i < count($bindings); $i++)
			{
				// If the binding is an array, we can assume it is being used to fill
				// a "where in" condition, so we will replace the next place-holder
				// in the query with the correct number of parameters based on the
				// number of elements in this binding.
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
	 * Execute a prepared PDO statement and return the appropriate results.
	 *
	 * The method returns the following based on query type:
	 *
	 *     SELECT -> Array of stdClasses
	 *     UPDATE -> Number of rows affected.
	 *     DELETE -> Number of Rows affected.
	 *     ELSE   -> Boolean true / false depending on success.
	 *
	 * @param  PDOStatement  $statement
	 * @param  array         $bindings
	 * @return mixed
	 */
	public function execute(PDOStatement $statement, $bindings)
	{
		$time = microtime(true);

		$result = $statement->execute($bindings);

		// Every query is timed so that we can log the executinon time along
		// with the query SQL and array of bindings. This should be make it
		// convenient for the developer to profile the application's query
		// performance to diagnose bottlenecks.
		$time = number_format((microtime(true) - $time) * 1000, 2);

		$sql = strtoupper($statement->queryString);

		// All of the queries executed across all connections are stored in
		// an array of queries so that the SQL, bindings, and the execution
		// time can all be easily retrieved for profiling the application.
		static::$queries[] = compact('sql', 'bindings', 'time');

		// The return type of the method depends on the type of query that
		// is executed against the database. For SELECT queries, we will
		// return the record set. For UPDATE and DELETE statements, the
		// number of affected rows will be returned. All other types of
		// queries will return the boolean result given by PDO.
		if (strpos($sql, 'SELECT') === 0)
		{
			return $statement->fetchAll(PDO::FETCH_CLASS, 'stdClass');
		}
		elseif (strpos($sql, 'UPDATE') === 0 or strpos($sql, 'DELETE') === 0)
		{
			return $statement->rowCount();
		}

		return $result;
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