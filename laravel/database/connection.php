<?php namespace Laravel\Database;

use PDO, PDOStatement, Laravel\Config, Laravel\Event;

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
	public $config;

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

		if (isset(\Laravel\Database::$registrar[$this->driver()]))
		{
			\Laravel\Database::$registrar[$this->driver()]['query']();
		}

		switch ($this->driver())
		{
			case 'mysql':
				return $this->grammar = new Query\Grammars\MySQL($this);

			case 'sqlite':
				return $this->grammar = new Query\Grammars\SQLite($this);

			case 'sqlsrv':
				return $this->grammar = new Query\Grammars\SQLServer($this);

			case 'pgsql':
				return $this->grammar = new Query\Grammars\Postgres($this);

			default:
				return $this->grammar = new Query\Grammars\Grammar($this);
		}
	}

	/**
	 * Execute a callback wrapped in a database transaction.
	 *
	 * @param  callback  $callback
	 * @return void
	 */
	public function transaction($callback)
	{
		$this->pdo->beginTransaction();

		// After beginning the database transaction, we will call the callback
		// so that it can do its database work. If an exception occurs we'll
		// rollback the transaction and re-throw back to the developer.
		try
		{
			call_user_func($callback);
		}
		catch (\Exception $e)
		{
			$this->pdo->rollBack();

			throw $e;
		}

		$this->pdo->commit();
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
		$sql = trim($sql);

		list($statement, $result) = $this->execute($sql, $bindings);

		// The result we return depends on the type of query executed against the
		// database. On SELECT clauses, we will return the result set, for update
		// and deletes we will return the affected row count.
		if (stripos($sql, 'select') === 0)
		{
			return $this->fetch($statement, Config::get('database.fetch'));
		}
		elseif (stripos($sql, 'update') === 0 or stripos($sql, 'delete') === 0)
		{
			return $statement->rowCount();
		}
		// For insert statements that use the "returning" clause, which is allowed
		// by database systems such as Postgres, we need to actually return the
		// real query result so the consumer can get the ID.
		elseif (stripos($sql, 'insert') === 0 and stripos($sql, 'returning') !== false)
		{
			return $this->fetch($statement, Config::get('database.fetch'));
		}
		else
		{
			return $result;
		}
	}

	/**
	 * Execute a SQL query against the connection.
	 *
	 * The PDO statement and boolean result will be returned in an array.
	 *
	 * @param  string  $sql
	 * @param  array   $bindings
	 * @return array
	 */
	protected function execute($sql, $bindings = array())
	{
		$bindings = (array) $bindings;

		// Since expressions are injected into the query as strings, we need to
		// remove them from the array of bindings. After we have removed them,
		// we'll reset the array so there are not gaps within the keys.
		$bindings = array_filter($bindings, function($binding)
		{
			return ! $binding instanceof Expression;
		});

		$bindings = array_values($bindings);

		$sql = $this->grammar()->shortcut($sql, $bindings);

		// Next we need to translate all DateTime bindings to their date-time
		// strings that are compatible with the database. Each grammar may
		// define it's own date-time format according to its needs.
		$datetime = $this->grammar()->datetime;

		for ($i = 0; $i < count($bindings); $i++)
		{
			if ($bindings[$i] instanceof \DateTime)
			{
				$bindings[$i] = $bindings[$i]->format($datetime);
			}
		}

		// Each database operation is wrapped in a try / catch so we can wrap
		// any database exceptions in our custom exception class, which will
		// set the message to include the SQL and query bindings.
		try
		{
			$statement = $this->pdo->prepare($sql);

			$start = microtime(true);

			$result = $statement->execute($bindings);
		}
		// If an exception occurs, we'll pass it into our custom exception
		// and set the message to include the SQL and query bindings so
		// debugging is much easier on the developer.
		catch (\Exception $exception)
		{
			$exception = new Exception($sql, $bindings, $exception);

			throw $exception;
		}

		// Once we have executed the query, we log the SQL, bindings, and
		// execution time in a static array that is accessed by all of
		// the connections actively being used by the application.
		if (Config::get('database.profile'))
		{
			$this->log($sql, $bindings, $start);
		}

		return array($statement, $result);
	}

	/**
	 * Fetch all of the rows for a given statement.
	 *
	 * @param  PDOStatement  $statement
	 * @param  int           $style
	 * @return array
	 */
	protected function fetch($statement, $style)
	{
		// If the fetch style is "class", we'll hydrate an array of PHP
		// stdClass objects as generic containers for the query rows,
		// otherwise we'll just use the fetch style value.
		if ($style === PDO::FETCH_CLASS)
		{
			return $statement->fetchAll(PDO::FETCH_CLASS, 'stdClass');
		}
		else
		{
			return $statement->fetchAll($style);
		}
	}

	/**
	 * Log the query and fire the core query event.
	 *
	 * @param  string  $sql
	 * @param  array   $bindings
	 * @param  int     $start
	 * @return void
	 */
	protected function log($sql, $bindings, $start)
	{
		$time = number_format((microtime(true) - $start) * 1000, 2);

		Event::fire('laravel.query', array($sql, $bindings, $time));

		static::$queries[] = compact('sql', 'bindings', 'time');
	}

	/**
	 * Get the driver name for the database connection.
	 *
	 * @return string
	 */
	public function driver()
	{
		return $this->config['driver'];
	}

	/**
	 * Magic Method for dynamically beginning queries on database tables.
	 */
	public function __call($method, $parameters)
	{
		return $this->table($method);
	}

}
