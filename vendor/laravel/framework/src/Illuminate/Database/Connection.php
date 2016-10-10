<?php namespace Illuminate\Database;

use PDO;
use Closure;
use DateTime;
use Illuminate\Database\Query\Processors\Processor;
use Doctrine\DBAL\Connection as DoctrineConnection;

class Connection implements ConnectionInterface {

	/**
	 * The active PDO connection.
	 *
	 * @var PDO
	 */
	protected $pdo;

	/**
	 * The active PDO connection used for reads.
	 *
	 * @var PDO
	 */
	protected $readPdo;

	/**
	 * The query grammar implementation.
	 *
	 * @var \Illuminate\Database\Query\Grammars\Grammar
	 */
	protected $queryGrammar;

	/**
	 * The schema grammar implementation.
	 *
	 * @var \Illuminate\Database\Schema\Grammars\Grammar
	 */
	protected $schemaGrammar;

	/**
	 * The query post processor implementation.
	 *
	 * @var \Illuminate\Database\Query\Processors\Processor
	 */
	protected $postProcessor;

	/**
	 * The event dispatcher instance.
	 *
	 * @var \Illuminate\Events\Dispatcher
	 */
	protected $events;

	/**
	 * The paginator environment instance.
	 *
	 * @var \Illuminate\Pagination\Paginator
	 */
	protected $paginator;

	/**
	 * The cache manager instance.
	 *
	 * @var \Illuminate\Cache\CacheManager
	 */
	protected $cache;

	/**
	 * The default fetch mode of the connection.
	 *
	 * @var int
	 */
	protected $fetchMode = PDO::FETCH_ASSOC;

	/**
	 * The number of active transactions.
	 *
	 * @var int
	 */
	protected $transactions = 0;

	/**
	 * All of the queries run against the connection.
	 *
	 * @var array
	 */
	protected $queryLog = array();

	/**
	 * Indicates whether queries are being logged.
	 *
	 * @var bool
	 */
	protected $loggingQueries = true;

	/**
	 * Indicates if the connection is in a "dry run".
	 *
	 * @var bool
	 */
	protected $pretending = false;

	/**
	 * The name of the connected database.
	 *
	 * @var string
	 */
	protected $database;

	/**
	 * The table prefix for the connection.
	 *
	 * @var string
	 */
	protected $tablePrefix = '';

	/**
	 * The database connection configuration options.
	 *
	 * @var array
	 */
	protected $config = array();

	/**
	 * Create a new database connection instance.
	 *
	 * @param  PDO     $pdo
	 * @param  string  $database
	 * @param  string  $tablePrefix
	 * @param  array   $config
	 * @return void
	 */
	public function __construct(PDO $pdo, $database = '', $tablePrefix = '', array $config = array())
	{
		$this->pdo = $pdo;

		// First we will setup the default properties. We keep track of the DB
		// name we are connected to since it is needed when some reflective
		// type commands are run such as checking whether a table exists.
		$this->database = $database;

		$this->tablePrefix = $tablePrefix;

		$this->config = $config;

		// We need to initialize a query grammar and the query post processors
		// which are both very important parts of the database abstractions
		// so we initialize these to their default values while starting.
		$this->useDefaultQueryGrammar();

		$this->useDefaultPostProcessor();
	}

	/**
	 * Set the query grammar to the default implementation.
	 *
	 * @return void
	 */
	public function useDefaultQueryGrammar()
	{
		$this->queryGrammar = $this->getDefaultQueryGrammar();
	}

	/**
	 * Get the default query grammar instance.
	 *
	 * @return \Illuminate\Database\Query\Grammars\Grammar
	 */
	protected function getDefaultQueryGrammar()
	{
		return new Query\Grammars\Grammar;
	}

	/**
	 * Set the schema grammar to the default implementation.
	 *
	 * @return void
	 */
	public function useDefaultSchemaGrammar()
	{
		$this->schemaGrammar = $this->getDefaultSchemaGrammar();
	}

	/**
	 * Get the default schema grammar instance.
	 *
	 * @return \Illuminate\Database\Schema\Grammars\Grammar
	 */
	protected function getDefaultSchemaGrammar() {}

	/**
	 * Set the query post processor to the default implementation.
	 *
	 * @return void
	 */
	public function useDefaultPostProcessor()
	{
		$this->postProcessor = $this->getDefaultPostProcessor();
	}

	/**
	 * Get the default post processor instance.
	 *
	 * @return \Illuminate\Database\Query\Processors\Processor
	 */
	protected function getDefaultPostProcessor()
	{
		return new Query\Processors\Processor;
	}

	/**
	 * Get a schema builder instance for the connection.
	 *
	 * @return \Illuminate\Database\Schema\Builder
	 */
	public function getSchemaBuilder()
	{
		if (is_null($this->schemaGrammar)) { $this->useDefaultSchemaGrammar(); }

		return new Schema\Builder($this);
	}

	/**
	 * Begin a fluent query against a database table.
	 *
	 * @param  string  $table
	 * @return \Illuminate\Database\Query\Builder
	 */
	public function table($table)
	{
		$processor = $this->getPostProcessor();

		$query = new Query\Builder($this, $this->getQueryGrammar(), $processor);

		return $query->from($table);
	}

	/**
	 * Get a new raw query expression.
	 *
	 * @param  mixed  $value
	 * @return \Illuminate\Database\Query\Expression
	 */
	public function raw($value)
	{
		return new Query\Expression($value);
	}

	/**
	 * Run a select statement and return a single result.
	 *
	 * @param  string  $query
	 * @param  array   $bindings
	 * @return mixed
	 */
	public function selectOne($query, $bindings = array())
	{
		$records = $this->select($query, $bindings);

		return count($records) > 0 ? reset($records) : null;
	}

	/**
	 * Run a select statement against the database.
	 *
	 * @param  string  $query
	 * @param  array   $bindings
	 * @return array
	 */
	public function select($query, $bindings = array())
	{
		return $this->run($query, $bindings, function($me, $query, $bindings)
		{
			if ($me->pretending()) return array();

			// For select statements, we'll simply execute the query and return an array
			// of the database result set. Each element in the array will be a single
			// row from the database table, and will either be an array or objects.
			$statement = $me->getReadPdo()->prepare($query);

			$statement->execute($me->prepareBindings($bindings));

			return $statement->fetchAll($me->getFetchMode());
		});
	}

	/**
	 * Run an insert statement against the database.
	 *
	 * @param  string  $query
	 * @param  array   $bindings
	 * @return bool
	 */
	public function insert($query, $bindings = array())
	{
		return $this->statement($query, $bindings);
	}

	/**
	 * Run an update statement against the database.
	 *
	 * @param  string  $query
	 * @param  array   $bindings
	 * @return int
	 */
	public function update($query, $bindings = array())
	{
		return $this->affectingStatement($query, $bindings);
	}

	/**
	 * Run a delete statement against the database.
	 *
	 * @param  string  $query
	 * @param  array   $bindings
	 * @return int
	 */
	public function delete($query, $bindings = array())
	{
		return $this->affectingStatement($query, $bindings);
	}

	/**
	 * Execute an SQL statement and return the boolean result.
	 *
	 * @param  string  $query
	 * @param  array   $bindings
	 * @return bool
	 */
	public function statement($query, $bindings = array())
	{
		return $this->run($query, $bindings, function($me, $query, $bindings)
		{
			if ($me->pretending()) return true;

			$bindings = $me->prepareBindings($bindings);

			return $me->getPdo()->prepare($query)->execute($bindings);
		});
	}

	/**
	 * Run an SQL statement and get the number of rows affected.
	 *
	 * @param  string  $query
	 * @param  array   $bindings
	 * @return int
	 */
	public function affectingStatement($query, $bindings = array())
	{
		return $this->run($query, $bindings, function($me, $query, $bindings)
		{
			if ($me->pretending()) return 0;

			// For update or delete statements, we want to get the number of rows affected
			// by the statement and return that back to the developer. We'll first need
			// to execute the statement and then we'll use PDO to fetch the affected.
			$statement = $me->getPdo()->prepare($query);

			$statement->execute($me->prepareBindings($bindings));

			return $statement->rowCount();
		});
	}

	/**
	 * Run a raw, unprepared query against the PDO connection.
	 *
	 * @param  string  $query
	 * @return bool
	 */
	public function unprepared($query)
	{
		return $this->run($query, array(), function($me, $query)
		{
			if ($me->pretending()) return true;

			return (bool) $me->getPdo()->exec($query);
		});
	}

	/**
	 * Prepare the query bindings for execution.
	 *
	 * @param  array  $bindings
	 * @return array
	 */
	public function prepareBindings(array $bindings)
	{
		$grammar = $this->getQueryGrammar();

		foreach ($bindings as $key => $value)
		{
			// We need to transform all instances of the DateTime class into an actual
			// date string. Each query grammar maintains its own date string format
			// so we'll just ask the grammar for the format to get from the date.
			if ($value instanceof DateTime)
			{
				$bindings[$key] = $value->format($grammar->getDateFormat());
			}
			elseif ($value === false)
			{
				$bindings[$key] = 0;
			}
		}

		return $bindings;
	}

	/**
	 * Execute a Closure within a transaction.
	 *
	 * @param  Closure  $callback
	 * @return mixed
	 *
	 * @throws \Exception
	 */
	public function transaction(Closure $callback)
	{
		$this->beginTransaction();

		// We'll simply execute the given callback within a try / catch block
		// and if we catch any exception we can rollback the transaction
		// so that none of the changes are persisted to the database.
		try
		{
			$result = $callback($this);

			$this->commit();
		}

		// If we catch an exception, we will roll back so nothing gets messed
		// up in the database. Then we'll re-throw the exception so it can
		// be handled how the developer sees fit for their applications.
		catch (\Exception $e)
		{
			$this->rollBack();

			throw $e;
		}

		return $result;
	}

	/**
	 * Start a new database transaction.
	 *
	 * @return void
	 */
	public function beginTransaction()
	{
		++$this->transactions;

		if ($this->transactions == 1)
		{
			$this->pdo->beginTransaction();
		}

		$this->fireConnectionEvent('beganTransaction');
	}

	/**
	 * Commit the active database transaction.
	 *
	 * @return void
	 */
	public function commit()
	{
		if ($this->transactions == 1) $this->pdo->commit();

		--$this->transactions;

		$this->fireConnectionEvent('committed');
	}

	/**
	 * Rollback the active database transaction.
	 *
	 * @return void
	 */
	public function rollBack()
	{
		if ($this->transactions == 1)
		{
			$this->transactions = 0;

			$this->pdo->rollBack();
		}
		else
		{
			--$this->transactions;
		}

		$this->fireConnectionEvent('rollingBack');
	}

	/**
	 * Get the number of active transactions.
	 *
	 * @return int
	 */
	public function transactionLevel()
	{
		return $this->transactions;
	}

	/**
	 * Execute the given callback in "dry run" mode.
	 *
	 * @param  Closure  $callback
	 * @return array
	 */
	public function pretend(Closure $callback)
	{
		$this->pretending = true;

		$this->queryLog = array();

		// Basically to make the database connection "pretend", we will just return
		// the default values for all the query methods, then we will return an
		// array of queries that were "executed" within the Closure callback.
		$callback($this);

		$this->pretending = false;

		return $this->queryLog;
	}

	/**
	 * Run a SQL statement and log its execution context.
	 *
	 * @param  string   $query
	 * @param  array    $bindings
	 * @param  Closure  $callback
	 * @return mixed
	 *
	 * @throws QueryException
	 */
	protected function run($query, $bindings, Closure $callback)
	{
		$start = microtime(true);

		// To execute the statement, we'll simply call the callback, which will actually
		// run the SQL against the PDO connection. Then we can calculate the time it
		// took to execute and log the query SQL, bindings and time in our memory.
		try
		{
			$result = $callback($this, $query, $bindings);
		}

		// If an exception occurs when attempting to run a query, we'll format the error
		// message to include the bindings with SQL, which will make this exception a
		// lot more helpful to the developer instead of just the database's errors.
		catch (\Exception $e)
		{
			throw new QueryException($query, $this->prepareBindings($bindings), $e);
		}

		// Once we have run the query we will calculate the time that it took to run and
		// then log the query, bindings, and execution time so we will report them on
		// the event that the developer needs them. We'll log time in milliseconds.
		$time = $this->getElapsedTime($start);

		$this->logQuery($query, $bindings, $time);

		return $result;
	}

	/**
	 * Log a query in the connection's query log.
	 *
	 * @param  string  $query
	 * @param  array   $bindings
	 * @param  $time
	 * @return void
	 */
	public function logQuery($query, $bindings, $time = null)
	{
		if (isset($this->events))
		{
			$this->events->fire('illuminate.query', array($query, $bindings, $time, $this->getName()));
		}

		if ( ! $this->loggingQueries) return;

		$this->queryLog[] = compact('query', 'bindings', 'time');
	}

	/**
	 * Register a database query listener with the connection.
	 *
	 * @param  Closure  $callback
	 * @return void
	 */
	public function listen(Closure $callback)
	{
		if (isset($this->events))
		{
			$this->events->listen('illuminate.query', $callback);
		}
	}

	/**
	 * Fire an event for this connection.
	 *
	 * @param  string  $event
	 * @return void
	 */
	protected function fireConnectionEvent($event)
	{
		if (isset($this->events))
		{
			$this->events->fire('connection.'.$this->getName().'.'.$event, $this);
		}
	}

	/**
	 * Get the elapsed time since a given starting point.
	 *
	 * @param  int    $start
	 * @return float
	 */
	protected function getElapsedTime($start)
	{
		return round((microtime(true) - $start) * 1000, 2);
	}

	/**
	 * Get a Doctrine Schema Column instance.
	 *
	 * @param  string  $table
	 * @param  string  $column
	 * @return \Doctrine\DBAL\Schema\Column
	 */
	public function getDoctrineColumn($table, $column)
	{
		$schema = $this->getDoctrineSchemaManager();

		return $schema->listTableDetails($table)->getColumn($column);
	}

	/**
	 * Get the Doctrine DBAL schema manager for the connection.
	 *
	 * @return \Doctrine\DBAL\Schema\AbstractSchemaManager
	 */
	public function getDoctrineSchemaManager()
	{
		return $this->getDoctrineDriver()->getSchemaManager($this->getDoctrineConnection());
	}

	/**
	 * Get the Doctrine DBAL database connection instance.
	 *
	 * @return \Doctrine\DBAL\Connection
	 */
	public function getDoctrineConnection()
	{
		$driver = $this->getDoctrineDriver();

		$data = array('pdo' => $this->pdo, 'dbname' => $this->getConfig('database'));

		return new DoctrineConnection($data, $driver);
	}

	/**
	 * Get the current PDO connection.
	 *
	 * @return PDO
	 */
	public function getPdo()
	{
		return $this->pdo;
	}

	/**
	 * Get the current PDO connection used for reading.
	 *
	 * @return PDO
	 */
	public function getReadPdo()
	{
		if ($this->transactions >= 1) return $this->getPdo();

		return $this->readPdo ?: $this->pdo;
	}

	/**
	 * Set the PDO connection.
	 *
	 * @param  PDO  $pdo
	 * @return \Illuminate\Database\Connection
	 */
	public function setPdo(PDO $pdo)
	{
		$this->pdo = $pdo;

		return $this;
	}

	/**
	 * Set the PDO connection used for reading.
	 *
	 * @param  PDO  $pdo
	 * @return \Illuminate\Database\Connection
	 */
	public function setReadPdo(PDO $pdo)
	{
		$this->readPdo = $pdo;

		return $this;
	}

	/**
	 * Get the database connection name.
	 *
	 * @return string|null
	 */
	public function getName()
	{
		return $this->getConfig('name');
	}

	/**
	 * Get an option from the configuration options.
	 *
	 * @param  string  $option
	 * @return mixed
	 */
	public function getConfig($option)
	{
		return array_get($this->config, $option);
	}

	/**
	 * Get the PDO driver name.
	 *
	 * @return string
	 */
	public function getDriverName()
	{
		return $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
	}

	/**
	 * Get the query grammar used by the connection.
	 *
	 * @return \Illuminate\Database\Query\Grammars\Grammar
	 */
	public function getQueryGrammar()
	{
		return $this->queryGrammar;
	}

	/**
	 * Set the query grammar used by the connection.
	 *
	 * @param  \Illuminate\Database\Query\Grammars\Grammar
	 * @return void
	 */
	public function setQueryGrammar(Query\Grammars\Grammar $grammar)
	{
		$this->queryGrammar = $grammar;
	}

	/**
	 * Get the schema grammar used by the connection.
	 *
	 * @return \Illuminate\Database\Query\Grammars\Grammar
	 */
	public function getSchemaGrammar()
	{
		return $this->schemaGrammar;
	}

	/**
	 * Set the schema grammar used by the connection.
	 *
	 * @param  \Illuminate\Database\Schema\Grammars\Grammar
	 * @return void
	 */
	public function setSchemaGrammar(Schema\Grammars\Grammar $grammar)
	{
		$this->schemaGrammar = $grammar;
	}

	/**
	 * Get the query post processor used by the connection.
	 *
	 * @return \Illuminate\Database\Query\Processors\Processor
	 */
	public function getPostProcessor()
	{
		return $this->postProcessor;
	}

	/**
	 * Set the query post processor used by the connection.
	 *
	 * @param  \Illuminate\Database\Query\Processors\Processor
	 * @return void
	 */
	public function setPostProcessor(Processor $processor)
	{
		$this->postProcessor = $processor;
	}

	/**
	 * Get the event dispatcher used by the connection.
	 *
	 * @return \Illuminate\Events\Dispatcher
	 */
	public function getEventDispatcher()
	{
		return $this->events;
	}

	/**
	 * Set the event dispatcher instance on the connection.
	 *
	 * @param  \Illuminate\Events\Dispatcher
	 * @return void
	 */
	public function setEventDispatcher(\Illuminate\Events\Dispatcher $events)
	{
		$this->events = $events;
	}

	/**
	 * Get the paginator environment instance.
	 *
	 * @return \Illuminate\Pagination\Environment
	 */
	public function getPaginator()
	{
		if ($this->paginator instanceof Closure)
		{
			$this->paginator = call_user_func($this->paginator);
		}

		return $this->paginator;
	}

	/**
	 * Set the pagination environment instance.
	 *
	 * @param  \Illuminate\Pagination\Environment|\Closure  $paginator
	 * @return void
	 */
	public function setPaginator($paginator)
	{
		$this->paginator = $paginator;
	}

	/**
	 * Get the cache manager instance.
	 *
	 * @return \Illuminate\Cache\CacheManager
	 */
	public function getCacheManager()
	{
		if ($this->cache instanceof Closure)
		{
			$this->cache = call_user_func($this->cache);
		}

		return $this->cache;
	}

	/**
	 * Set the cache manager instance on the connection.
	 *
	 * @param  \Illuminate\Cache\CacheManager|\Closure  $cache
	 * @return void
	 */
	public function setCacheManager($cache)
	{
		$this->cache = $cache;
	}

	/**
	 * Determine if the connection in a "dry run".
	 *
	 * @return bool
	 */
	public function pretending()
	{
		return $this->pretending === true;
	}

	/**
	 * Get the default fetch mode for the connection.
	 *
	 * @return int
	 */
	public function getFetchMode()
	{
		return $this->fetchMode;
	}

	/**
	 * Set the default fetch mode for the connection.
	 *
	 * @param  int  $fetchMode
	 * @return int
	 */
	public function setFetchMode($fetchMode)
	{
		$this->fetchMode = $fetchMode;
	}

	/**
	 * Get the connection query log.
	 *
	 * @return array
	 */
	public function getQueryLog()
	{
		return $this->queryLog;
	}

	/**
	 * Clear the query log.
	 *
	 * @return void
	 */
	public function flushQueryLog()
	{
		$this->queryLog = array();
	}

	/**
	 * Enable the query log on the connection.
	 *
	 * @return void
	 */
	public function enableQueryLog()
	{
		$this->loggingQueries = true;
	}

	/**
	 * Disable the query log on the connection.
	 *
	 * @return void
	 */
	public function disableQueryLog()
	{
		$this->loggingQueries = false;
	}

	/**
	 * Determine whether we're logging queries.
	 *
	 * @return bool
	 */
	public function logging()
	{
		return $this->loggingQueries;
	}

	/**
	 * Get the name of the connected database.
	 *
	 * @return string
	 */
	public function getDatabaseName()
	{
		return $this->database;
	}

	/**
	 * Set the name of the connected database.
	 *
	 * @param  string  $database
	 * @return string
	 */
	public function setDatabaseName($database)
	{
		$this->database = $database;
	}

	/**
	 * Get the table prefix for the connection.
	 *
	 * @return string
	 */
	public function getTablePrefix()
	{
		return $this->tablePrefix;
	}

	/**
	 * Set the table prefix in use by the connection.
	 *
	 * @param  string  $prefix
	 * @return void
	 */
	public function setTablePrefix($prefix)
	{
		$this->tablePrefix = $prefix;

		$this->getQueryGrammar()->setTablePrefix($prefix);
	}

	/**
	 * Set the table prefix and return the grammar.
	 *
	 * @param  \Illuminate\Database\Grammar  $grammar
	 * @return \Illuminate\Database\Grammar
	 */
	public function withTablePrefix(Grammar $grammar)
	{
		$grammar->setTablePrefix($this->tablePrefix);

		return $grammar;
	}

}
