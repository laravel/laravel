<?php

namespace Illuminate\Database;

use Carbon\CarbonInterval;
use Closure;
use DateTimeInterface;
use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\Events\StatementPrepared;
use Illuminate\Database\Events\TransactionBeginning;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Database\Events\TransactionCommitting;
use Illuminate\Database\Events\TransactionRolledBack;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\Grammars\Grammar as QueryGrammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Database\Schema\Builder as SchemaBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\InteractsWithTime;
use Illuminate\Support\Traits\Macroable;
use PDO;
use PDOStatement;
use RuntimeException;

use function Illuminate\Support\enum_value;

class Connection implements ConnectionInterface
{
    use DetectsConcurrencyErrors,
        DetectsLostConnections,
        Concerns\ManagesTransactions,
        InteractsWithTime,
        Macroable;

    /**
     * The active PDO connection.
     *
     * @var \PDO|(\Closure(): \PDO)
     */
    protected $pdo;

    /**
     * The active PDO connection used for reads.
     *
     * @var \PDO|(\Closure(): \PDO)
     */
    protected $readPdo;

    /**
     * The database connection configuration options for reading.
     *
     * @var array
     */
    protected $readPdoConfig = [];

    /**
     * The name of the connected database.
     *
     * @var string
     */
    protected $database;

    /**
     * The type of the connection.
     *
     * @var string|null
     */
    protected $readWriteType;

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
    protected $config = [];

    /**
     * The reconnector instance for the connection.
     *
     * @var (callable(\Illuminate\Database\Connection): mixed)
     */
    protected $reconnector;

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
     * @var \Illuminate\Contracts\Events\Dispatcher|null
     */
    protected $events;

    /**
     * The default fetch mode of the connection.
     *
     * @var int
     */
    protected $fetchMode = PDO::FETCH_OBJ;

    /**
     * The number of active transactions.
     *
     * @var int
     */
    protected $transactions = 0;

    /**
     * The transaction manager instance.
     *
     * @var \Illuminate\Database\DatabaseTransactionsManager|null
     */
    protected $transactionsManager;

    /**
     * Indicates if changes have been made to the database.
     *
     * @var bool
     */
    protected $recordsModified = false;

    /**
     * Indicates if the connection should use the "write" PDO connection.
     *
     * @var bool
     */
    protected $readOnWriteConnection = false;

    /**
     * All of the queries run against the connection.
     *
     * @var array{query: string, bindings: array, time: float|null}[]
     */
    protected $queryLog = [];

    /**
     * Indicates whether queries are being logged.
     *
     * @var bool
     */
    protected $loggingQueries = false;

    /**
     * The duration of all executed queries in milliseconds.
     *
     * @var float
     */
    protected $totalQueryDuration = 0.0;

    /**
     * All of the registered query duration handlers.
     *
     * @var array{has_run: bool, handler: (callable(\Illuminate\Database\Connection, class-string<\Illuminate\Database\Events\QueryExecuted>): mixed)}[]
     */
    protected $queryDurationHandlers = [];

    /**
     * Indicates if the connection is in a "dry run".
     *
     * @var bool
     */
    protected $pretending = false;

    /**
     * All of the callbacks that should be invoked before a transaction is started.
     *
     * @var \Closure[]
     */
    protected $beforeStartingTransaction = [];

    /**
     * All of the callbacks that should be invoked before a query is executed.
     *
     * @var (\Closure(string, array, \Illuminate\Database\Connection): mixed)[]
     */
    protected $beforeExecutingCallbacks = [];

    /**
     * The connection resolvers.
     *
     * @var \Closure[]
     */
    protected static $resolvers = [];

    /**
     * The last retrieved PDO read / write type.
     *
     * @var null|'read'|'write'
     */
    protected $latestPdoTypeRetrieved = null;

    /**
     * Create a new database connection instance.
     *
     * @param  \PDO|(\Closure(): \PDO)  $pdo
     * @param  string  $database
     * @param  string  $tablePrefix
     * @param  array  $config
     */
    public function __construct($pdo, $database = '', $tablePrefix = '', array $config = [])
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
        return new QueryGrammar($this);
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
     * @return \Illuminate\Database\Schema\Grammars\Grammar|null
     */
    protected function getDefaultSchemaGrammar()
    {
        //
    }

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
        return new Processor;
    }

    /**
     * Get a schema builder instance for the connection.
     *
     * @return \Illuminate\Database\Schema\Builder
     */
    public function getSchemaBuilder()
    {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        return new SchemaBuilder($this);
    }

    /**
     * Begin a fluent query against a database table.
     *
     * @param  \Closure|\Illuminate\Database\Query\Builder|\Illuminate\Contracts\Database\Query\Expression|\UnitEnum|string  $table
     * @param  string|null  $as
     * @return \Illuminate\Database\Query\Builder
     */
    public function table($table, $as = null)
    {
        return $this->query()->from(enum_value($table), $as);
    }

    /**
     * Get a new query builder instance.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function query()
    {
        return new QueryBuilder(
            $this, $this->getQueryGrammar(), $this->getPostProcessor()
        );
    }

    /**
     * Run a select statement and return a single result.
     *
     * @param  string  $query
     * @param  array  $bindings
     * @param  bool  $useReadPdo
     * @return mixed
     */
    public function selectOne($query, $bindings = [], $useReadPdo = true)
    {
        $records = $this->select($query, $bindings, $useReadPdo);

        return array_shift($records);
    }

    /**
     * Run a select statement and return the first column of the first row.
     *
     * @param  string  $query
     * @param  array  $bindings
     * @param  bool  $useReadPdo
     * @return mixed
     *
     * @throws \Illuminate\Database\MultipleColumnsSelectedException
     */
    public function scalar($query, $bindings = [], $useReadPdo = true)
    {
        $record = $this->selectOne($query, $bindings, $useReadPdo);

        if (is_null($record)) {
            return null;
        }

        $record = (array) $record;

        if (count($record) > 1) {
            throw new MultipleColumnsSelectedException;
        }

        return array_first($record);
    }

    /**
     * Run a select statement against the database.
     *
     * @param  string  $query
     * @param  array  $bindings
     * @return array
     */
    public function selectFromWriteConnection($query, $bindings = [])
    {
        return $this->select($query, $bindings, false);
    }

    /**
     * Run a select statement against the database.
     *
     * @param  string  $query
     * @param  array  $bindings
     * @param  bool  $useReadPdo
     * @return array
     */
    public function select($query, $bindings = [], $useReadPdo = true)
    {
        return $this->run($query, $bindings, function ($query, $bindings) use ($useReadPdo) {
            if ($this->pretending()) {
                return [];
            }

            // For select statements, we'll simply execute the query and return an array
            // of the database result set. Each element in the array will be a single
            // row from the database table, and will either be an array or objects.
            $statement = $this->prepared(
                $this->getPdoForSelect($useReadPdo)->prepare($query)
            );

            $this->bindValues($statement, $this->prepareBindings($bindings));

            $statement->execute();

            return $statement->fetchAll();
        });
    }

    /**
     * Run a select statement against the database and returns all of the result sets.
     *
     * @param  string  $query
     * @param  array  $bindings
     * @param  bool  $useReadPdo
     * @return array
     */
    public function selectResultSets($query, $bindings = [], $useReadPdo = true)
    {
        return $this->run($query, $bindings, function ($query, $bindings) use ($useReadPdo) {
            if ($this->pretending()) {
                return [];
            }

            $statement = $this->prepared(
                $this->getPdoForSelect($useReadPdo)->prepare($query)
            );

            $this->bindValues($statement, $this->prepareBindings($bindings));

            $statement->execute();

            $sets = [];

            do {
                $sets[] = $statement->fetchAll();
            } while ($statement->nextRowset());

            return $sets;
        });
    }

    /**
     * Run a select statement against the database and returns a generator.
     *
     * @param  string  $query
     * @param  array  $bindings
     * @param  bool  $useReadPdo
     * @return \Generator<int, \stdClass>
     */
    public function cursor($query, $bindings = [], $useReadPdo = true)
    {
        $statement = $this->run($query, $bindings, function ($query, $bindings) use ($useReadPdo) {
            if ($this->pretending()) {
                return [];
            }

            // First we will create a statement for the query. Then, we will set the fetch
            // mode and prepare the bindings for the query. Once that's done we will be
            // ready to execute the query against the database and return the cursor.
            $statement = $this->prepared($this->getPdoForSelect($useReadPdo)
                ->prepare($query));

            $this->bindValues(
                $statement, $this->prepareBindings($bindings)
            );

            // Next, we'll execute the query against the database and return the statement
            // so we can return the cursor. The cursor will use a PHP generator to give
            // back one row at a time without using a bunch of memory to render them.
            $statement->execute();

            return $statement;
        });

        while ($record = $statement->fetch()) {
            yield $record;
        }
    }

    /**
     * Configure the PDO prepared statement.
     *
     * @param  \PDOStatement  $statement
     * @return \PDOStatement
     */
    protected function prepared(PDOStatement $statement)
    {
        $statement->setFetchMode($this->fetchMode);

        $this->event(new StatementPrepared($this, $statement));

        return $statement;
    }

    /**
     * Get the PDO connection to use for a select query.
     *
     * @param  bool  $useReadPdo
     * @return \PDO
     */
    protected function getPdoForSelect($useReadPdo = true)
    {
        return $useReadPdo ? $this->getReadPdo() : $this->getPdo();
    }

    /**
     * Run an insert statement against the database.
     *
     * @param  string  $query
     * @param  array  $bindings
     * @return bool
     */
    public function insert($query, $bindings = [])
    {
        return $this->statement($query, $bindings);
    }

    /**
     * Run an update statement against the database.
     *
     * @param  string  $query
     * @param  array  $bindings
     * @return int
     */
    public function update($query, $bindings = [])
    {
        return $this->affectingStatement($query, $bindings);
    }

    /**
     * Run a delete statement against the database.
     *
     * @param  string  $query
     * @param  array  $bindings
     * @return int
     */
    public function delete($query, $bindings = [])
    {
        return $this->affectingStatement($query, $bindings);
    }

    /**
     * Execute an SQL statement and return the boolean result.
     *
     * @param  string  $query
     * @param  array  $bindings
     * @return bool
     */
    public function statement($query, $bindings = [])
    {
        return $this->run($query, $bindings, function ($query, $bindings) {
            if ($this->pretending()) {
                return true;
            }

            $statement = $this->getPdo()->prepare($query);

            $this->bindValues($statement, $this->prepareBindings($bindings));

            $this->recordsHaveBeenModified();

            return $statement->execute();
        });
    }

    /**
     * Run an SQL statement and get the number of rows affected.
     *
     * @param  string  $query
     * @param  array  $bindings
     * @return int
     */
    public function affectingStatement($query, $bindings = [])
    {
        return $this->run($query, $bindings, function ($query, $bindings) {
            if ($this->pretending()) {
                return 0;
            }

            // For update or delete statements, we want to get the number of rows affected
            // by the statement and return that back to the developer. We'll first need
            // to execute the statement and then we'll use PDO to fetch the affected.
            $statement = $this->getPdo()->prepare($query);

            $this->bindValues($statement, $this->prepareBindings($bindings));

            $statement->execute();

            $this->recordsHaveBeenModified(
                ($count = $statement->rowCount()) > 0
            );

            return $count;
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
        return $this->run($query, [], function ($query) {
            if ($this->pretending()) {
                return true;
            }

            $this->recordsHaveBeenModified(
                $change = $this->getPdo()->exec($query) !== false
            );

            return $change;
        });
    }

    /**
     * Get the number of open connections for the database.
     *
     * @return int|null
     */
    public function threadCount()
    {
        $query = $this->getQueryGrammar()->compileThreadCount();

        return $query ? $this->scalar($query) : null;
    }

    /**
     * Execute the given callback in "dry run" mode.
     *
     * @param  (\Closure(\Illuminate\Database\Connection): mixed)  $callback
     * @return array{query: string, bindings: array, time: float|null}[]
     */
    public function pretend(Closure $callback)
    {
        return $this->withFreshQueryLog(function () use ($callback) {
            $this->pretending = true;

            try {
                // Basically to make the database connection "pretend", we will just return
                // the default values for all the query methods, then we will return an
                // array of queries that were "executed" within the Closure callback.
                $callback($this);

                return $this->queryLog;
            } finally {
                $this->pretending = false;
            }
        });
    }

    /**
     * Execute the given callback without "pretending".
     *
     * @param  \Closure  $callback
     * @return mixed
     */
    public function withoutPretending(Closure $callback)
    {
        if (! $this->pretending) {
            return $callback();
        }

        $this->pretending = false;

        try {
            return $callback();
        } finally {
            $this->pretending = true;
        }
    }

    /**
     * Execute the given callback in "dry run" mode.
     *
     * @param  (\Closure(): array{query: string, bindings: array, time: float|null}[])  $callback
     * @return array{query: string, bindings: array, time: float|null}[]
     */
    protected function withFreshQueryLog($callback)
    {
        $loggingQueries = $this->loggingQueries;

        // First we will back up the value of the logging queries property and then
        // we'll be ready to run callbacks. This query log will also get cleared
        // so we will have a new log of all the queries that are executed now.
        $this->enableQueryLog();

        $this->queryLog = [];

        // Now we'll execute this callback and capture the result. Once it has been
        // executed we will restore the value of query logging and give back the
        // value of the callback so the original callers can have the results.
        $result = $callback();

        $this->loggingQueries = $loggingQueries;

        return $result;
    }

    /**
     * Bind values to their parameters in the given statement.
     *
     * @param  \PDOStatement  $statement
     * @param  array  $bindings
     * @return void
     */
    public function bindValues($statement, $bindings)
    {
        foreach ($bindings as $key => $value) {
            $statement->bindValue(
                is_string($key) ? $key : $key + 1,
                $value,
                match (true) {
                    is_int($value) => PDO::PARAM_INT,
                    is_resource($value) => PDO::PARAM_LOB,
                    default => PDO::PARAM_STR
                },
            );
        }
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

        foreach ($bindings as $key => $value) {
            // We need to transform all instances of DateTimeInterface into the actual
            // date string. Each query grammar maintains its own date string format
            // so we'll just ask the grammar for the format to get from the date.
            if ($value instanceof DateTimeInterface) {
                $bindings[$key] = $value->format($grammar->getDateFormat());
            } elseif (is_bool($value)) {
                $bindings[$key] = (int) $value;
            }
        }

        return $bindings;
    }

    /**
     * Run a SQL statement and log its execution context.
     *
     * @param  string  $query
     * @param  array  $bindings
     * @param  \Closure  $callback
     * @return mixed
     *
     * @throws \Illuminate\Database\QueryException
     */
    protected function run($query, $bindings, Closure $callback)
    {
        foreach ($this->beforeExecutingCallbacks as $beforeExecutingCallback) {
            $beforeExecutingCallback($query, $bindings, $this);
        }

        $this->reconnectIfMissingConnection();

        $start = microtime(true);

        // Here we will run this query. If an exception occurs we'll determine if it was
        // caused by a connection that has been lost. If that is the cause, we'll try
        // to re-establish connection and re-run the query with a fresh connection.
        try {
            $result = $this->runQueryCallback($query, $bindings, $callback);
        } catch (QueryException $e) {
            $result = $this->handleQueryException(
                $e, $query, $bindings, $callback
            );
        }

        // Once we have run the query we will calculate the time that it took to run and
        // then log the query, bindings, and execution time so we will report them on
        // the event that the developer needs them. We'll log time in milliseconds.
        $this->logQuery(
            $query, $bindings, $this->getElapsedTime($start)
        );

        return $result;
    }

    /**
     * Run a SQL statement.
     *
     * @param  string  $query
     * @param  array  $bindings
     * @param  \Closure  $callback
     * @return mixed
     *
     * @throws \Illuminate\Database\QueryException
     */
    protected function runQueryCallback($query, $bindings, Closure $callback)
    {
        // To execute the statement, we'll simply call the callback, which will actually
        // run the SQL against the PDO connection. Then we can calculate the time it
        // took to execute and log the query SQL, bindings and time in our memory.
        try {
            return $callback($query, $bindings);
        }

        // If an exception occurs when attempting to run a query, we'll format the error
        // message to include the bindings with SQL, which will make this exception a
        // lot more helpful to the developer instead of just the database's errors.
        catch (Exception $e) {
            $exceptionType = $this->isUniqueConstraintError($e)
                ? UniqueConstraintViolationException::class
                : QueryException::class;

            throw new $exceptionType(
                $this->getNameWithReadWriteType(),
                $query,
                $this->prepareBindings($bindings),
                $e,
                $this->getConnectionDetails(),
                $this->latestReadWriteTypeUsed(),
            );
        }
    }

    /**
     * Determine if the given database exception was caused by a unique constraint violation.
     *
     * @param  \Exception  $exception
     * @return bool
     */
    protected function isUniqueConstraintError(Exception $exception)
    {
        return false;
    }

    /**
     * Log a query in the connection's query log.
     *
     * @param  string  $query
     * @param  array  $bindings
     * @param  float|null  $time
     * @return void
     */
    public function logQuery($query, $bindings, $time = null)
    {
        $this->totalQueryDuration += $time ?? 0.0;

        $readWriteType = $this->latestReadWriteTypeUsed();

        $this->event(new QueryExecuted($query, $bindings, $time, $this, $readWriteType));

        $query = $this->pretending === true
            ? $this->queryGrammar?->substituteBindingsIntoRawSql($query, $bindings) ?? $query
            : $query;

        if ($this->loggingQueries) {
            $this->queryLog[] = compact('query', 'bindings', 'time', 'readWriteType');
        }
    }

    /**
     * Get the elapsed time in milliseconds since a given starting point.
     *
     * @param  float  $start
     * @return float
     */
    protected function getElapsedTime($start)
    {
        return round((microtime(true) - $start) * 1000, 2);
    }

    /**
     * Register a callback to be invoked when the connection queries for longer than a given amount of time.
     *
     * @param  \DateTimeInterface|\Carbon\CarbonInterval|float|int  $threshold
     * @param  (callable(\Illuminate\Database\Connection, \Illuminate\Database\Events\QueryExecuted): mixed)  $handler
     * @return void
     */
    public function whenQueryingForLongerThan($threshold, $handler)
    {
        $threshold = $threshold instanceof DateTimeInterface
            ? $this->secondsUntil($threshold) * 1000
            : $threshold;

        $threshold = $threshold instanceof CarbonInterval
            ? $threshold->totalMilliseconds
            : $threshold;

        $this->queryDurationHandlers[] = [
            'has_run' => false,
            'handler' => $handler,
        ];

        $key = count($this->queryDurationHandlers) - 1;

        $this->listen(function ($event) use ($threshold, $handler, $key) {
            if (! $this->queryDurationHandlers[$key]['has_run'] && $this->totalQueryDuration() > $threshold) {
                $handler($this, $event);

                $this->queryDurationHandlers[$key]['has_run'] = true;
            }
        });
    }

    /**
     * Allow all the query duration handlers to run again, even if they have already run.
     *
     * @return void
     */
    public function allowQueryDurationHandlersToRunAgain()
    {
        foreach ($this->queryDurationHandlers as $key => $queryDurationHandler) {
            $this->queryDurationHandlers[$key]['has_run'] = false;
        }
    }

    /**
     * Get the duration of all run queries in milliseconds.
     *
     * @return float
     */
    public function totalQueryDuration()
    {
        return $this->totalQueryDuration;
    }

    /**
     * Reset the duration of all run queries.
     *
     * @return void
     */
    public function resetTotalQueryDuration()
    {
        $this->totalQueryDuration = 0.0;
    }

    /**
     * Handle a query exception.
     *
     * @param  \Illuminate\Database\QueryException  $e
     * @param  string  $query
     * @param  array  $bindings
     * @param  \Closure  $callback
     * @return mixed
     *
     * @throws \Illuminate\Database\QueryException
     */
    protected function handleQueryException(QueryException $e, $query, $bindings, Closure $callback)
    {
        if ($this->transactions >= 1) {
            throw $e;
        }

        return $this->tryAgainIfCausedByLostConnection(
            $e, $query, $bindings, $callback
        );
    }

    /**
     * Handle a query exception that occurred during query execution.
     *
     * @param  \Illuminate\Database\QueryException  $e
     * @param  string  $query
     * @param  array  $bindings
     * @param  \Closure  $callback
     * @return mixed
     *
     * @throws \Illuminate\Database\QueryException
     */
    protected function tryAgainIfCausedByLostConnection(QueryException $e, $query, $bindings, Closure $callback)
    {
        if ($this->causedByLostConnection($e->getPrevious())) {
            $this->reconnect();

            return $this->runQueryCallback($query, $bindings, $callback);
        }

        throw $e;
    }

    /**
     * Reconnect to the database.
     *
     * @return mixed|false
     *
     * @throws \Illuminate\Database\LostConnectionException
     */
    public function reconnect()
    {
        if (is_callable($this->reconnector)) {
            return call_user_func($this->reconnector, $this);
        }

        throw new LostConnectionException('Lost connection and no reconnector available.');
    }

    /**
     * Reconnect to the database if a PDO connection is missing.
     *
     * @return void
     */
    public function reconnectIfMissingConnection()
    {
        if (is_null($this->pdo)) {
            $this->reconnect();
        }
    }

    /**
     * Disconnect from the underlying PDO connection.
     *
     * @return void
     */
    public function disconnect()
    {
        $this->setPdo(null)->setReadPdo(null);
    }

    /**
     * Register a hook to be run just before a database transaction is started.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function beforeStartingTransaction(Closure $callback)
    {
        $this->beforeStartingTransaction[] = $callback;

        return $this;
    }

    /**
     * Register a hook to be run just before a database query is executed.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function beforeExecuting(Closure $callback)
    {
        $this->beforeExecutingCallbacks[] = $callback;

        return $this;
    }

    /**
     * Register a database query listener with the connection.
     *
     * @param  \Closure(\Illuminate\Database\Events\QueryExecuted)  $callback
     * @return void
     */
    public function listen(Closure $callback)
    {
        $this->events?->listen(Events\QueryExecuted::class, $callback);
    }

    /**
     * Fire an event for this connection.
     *
     * @param  string  $event
     * @return array|null
     */
    protected function fireConnectionEvent($event)
    {
        return $this->events?->dispatch(match ($event) {
            'beganTransaction' => new TransactionBeginning($this),
            'committed' => new TransactionCommitted($this),
            'committing' => new TransactionCommitting($this),
            'rollingBack' => new TransactionRolledBack($this),
            default => null,
        });
    }

    /**
     * Fire the given event if possible.
     *
     * @param  mixed  $event
     * @return void
     */
    protected function event($event)
    {
        $this->events?->dispatch($event);
    }

    /**
     * Get a new raw query expression.
     *
     * @param  mixed  $value
     * @return \Illuminate\Contracts\Database\Query\Expression
     */
    public function raw($value)
    {
        return new Expression($value);
    }

    /**
     * Escape a value for safe SQL embedding.
     *
     * @param  string|float|int|bool|null  $value
     * @param  bool  $binary
     * @return string
     *
     * @throws \RuntimeException
     */
    public function escape($value, $binary = false)
    {
        if ($value === null) {
            return 'null';
        } elseif ($binary) {
            return $this->escapeBinary($value);
        } elseif (is_int($value) || is_float($value)) {
            return (string) $value;
        } elseif (is_bool($value)) {
            return $this->escapeBool($value);
        } elseif (is_array($value)) {
            throw new RuntimeException('The database connection does not support escaping arrays.');
        } else {
            if (str_contains($value, "\00")) {
                throw new RuntimeException('Strings with null bytes cannot be escaped. Use the binary escape option.');
            }

            if (preg_match('//u', $value) === false) {
                throw new RuntimeException('Strings with invalid UTF-8 byte sequences cannot be escaped.');
            }

            return $this->escapeString($value);
        }
    }

    /**
     * Escape a string value for safe SQL embedding.
     *
     * @param  string  $value
     * @return string
     */
    protected function escapeString($value)
    {
        return $this->getReadPdo()->quote($value);
    }

    /**
     * Escape a boolean value for safe SQL embedding.
     *
     * @param  bool  $value
     * @return string
     */
    protected function escapeBool($value)
    {
        return $value ? '1' : '0';
    }

    /**
     * Escape a binary value for safe SQL embedding.
     *
     * @param  string  $value
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function escapeBinary($value)
    {
        throw new RuntimeException('The database connection does not support escaping binary values.');
    }

    /**
     * Determine if the database connection has modified any database records.
     *
     * @return bool
     */
    public function hasModifiedRecords()
    {
        return $this->recordsModified;
    }

    /**
     * Indicate if any records have been modified.
     *
     * @param  bool  $value
     * @return void
     */
    public function recordsHaveBeenModified($value = true)
    {
        if (! $this->recordsModified) {
            $this->recordsModified = $value;
        }
    }

    /**
     * Set the record modification state.
     *
     * @param  bool  $value
     * @return $this
     */
    public function setRecordModificationState(bool $value)
    {
        $this->recordsModified = $value;

        return $this;
    }

    /**
     * Reset the record modification state.
     *
     * @return void
     */
    public function forgetRecordModificationState()
    {
        $this->recordsModified = false;
    }

    /**
     * Indicate that the connection should use the write PDO connection for reads.
     *
     * @param  bool  $value
     * @return $this
     */
    public function useWriteConnectionWhenReading($value = true)
    {
        $this->readOnWriteConnection = $value;

        return $this;
    }

    /**
     * Get the current PDO connection.
     *
     * @return \PDO
     */
    public function getPdo()
    {
        $this->latestPdoTypeRetrieved = 'write';

        if ($this->pdo instanceof Closure) {
            return $this->pdo = call_user_func($this->pdo);
        }

        return $this->pdo;
    }

    /**
     * Get the current PDO connection parameter without executing any reconnect logic.
     *
     * @return \PDO|\Closure|null
     */
    public function getRawPdo()
    {
        return $this->pdo;
    }

    /**
     * Get the current PDO connection used for reading.
     *
     * @return \PDO
     */
    public function getReadPdo()
    {
        if ($this->transactions > 0) {
            return $this->getPdo();
        }

        if ($this->readOnWriteConnection ||
            ($this->recordsModified && $this->getConfig('sticky'))) {
            return $this->getPdo();
        }

        $this->latestPdoTypeRetrieved = 'read';

        if ($this->readPdo instanceof Closure) {
            return $this->readPdo = call_user_func($this->readPdo);
        }

        return $this->readPdo ?: $this->getPdo();
    }

    /**
     * Get the current read PDO connection parameter without executing any reconnect logic.
     *
     * @return \PDO|\Closure|null
     */
    public function getRawReadPdo()
    {
        return $this->readPdo;
    }

    /**
     * Set the PDO connection.
     *
     * @param  \PDO|\Closure|null  $pdo
     * @return $this
     */
    public function setPdo($pdo)
    {
        $this->transactions = 0;

        $this->pdo = $pdo;

        return $this;
    }

    /**
     * Set the PDO connection used for reading.
     *
     * @param  \PDO|\Closure|null  $pdo
     * @return $this
     */
    public function setReadPdo($pdo)
    {
        $this->readPdo = $pdo;

        return $this;
    }

    /**
     * Set the read PDO connection configuration.
     *
     * @param  array  $config
     * @return $this
     */
    public function setReadPdoConfig(array $config)
    {
        $this->readPdoConfig = $config;

        return $this;
    }

    /**
     * Set the reconnect instance on the connection.
     *
     * @param  (callable(\Illuminate\Database\Connection): mixed)  $reconnector
     * @return $this
     */
    public function setReconnector(callable $reconnector)
    {
        $this->reconnector = $reconnector;

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
     * Get the database connection with its read / write type.
     *
     * @return string|null
     */
    public function getNameWithReadWriteType()
    {
        $name = $this->getName().($this->readWriteType ? '::'.$this->readWriteType : '');

        return empty($name) ? null : $name;
    }

    /**
     * Get an option from the configuration options.
     *
     * @param  string|null  $option
     * @return mixed
     */
    public function getConfig($option = null)
    {
        return Arr::get($this->config, $option);
    }

    /**
     * Get the basic connection information as an array for debugging.
     *
     * @return array
     */
    protected function getConnectionDetails()
    {
        $config = $this->latestReadWriteTypeUsed() === 'read'
            ? $this->readPdoConfig
            : $this->config;

        return [
            'driver' => $this->getDriverName(),
            'name' => $this->getNameWithReadWriteType(),
            'host' => $config['host'] ?? null,
            'port' => $config['port'] ?? null,
            'database' => $config['database'] ?? null,
            'unix_socket' => $config['unix_socket'] ?? null,
        ];
    }

    /**
     * Get the PDO driver name.
     *
     * @return string
     */
    public function getDriverName()
    {
        return $this->getConfig('driver');
    }

    /**
     * Get a human-readable name for the given connection driver.
     *
     * @return string
     */
    public function getDriverTitle()
    {
        return $this->getDriverName();
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
     * @param  \Illuminate\Database\Query\Grammars\Grammar  $grammar
     * @return $this
     */
    public function setQueryGrammar(Query\Grammars\Grammar $grammar)
    {
        $this->queryGrammar = $grammar;

        return $this;
    }

    /**
     * Get the schema grammar used by the connection.
     *
     * @return \Illuminate\Database\Schema\Grammars\Grammar
     */
    public function getSchemaGrammar()
    {
        return $this->schemaGrammar;
    }

    /**
     * Set the schema grammar used by the connection.
     *
     * @param  \Illuminate\Database\Schema\Grammars\Grammar  $grammar
     * @return $this
     */
    public function setSchemaGrammar(Schema\Grammars\Grammar $grammar)
    {
        $this->schemaGrammar = $grammar;

        return $this;
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
     * @param  \Illuminate\Database\Query\Processors\Processor  $processor
     * @return $this
     */
    public function setPostProcessor(Processor $processor)
    {
        $this->postProcessor = $processor;

        return $this;
    }

    /**
     * Get the event dispatcher used by the connection.
     *
     * @return \Illuminate\Contracts\Events\Dispatcher|null
     */
    public function getEventDispatcher()
    {
        return $this->events;
    }

    /**
     * Set the event dispatcher instance on the connection.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return $this
     */
    public function setEventDispatcher(Dispatcher $events)
    {
        $this->events = $events;

        return $this;
    }

    /**
     * Unset the event dispatcher for this connection.
     *
     * @return void
     */
    public function unsetEventDispatcher()
    {
        $this->events = null;
    }

    /**
     * Run the statement to start a new transaction.
     *
     * @return void
     */
    protected function executeBeginTransactionStatement()
    {
        $this->getPdo()->beginTransaction();
    }

    /**
     * Set the transaction manager instance on the connection.
     *
     * @param  \Illuminate\Database\DatabaseTransactionsManager  $manager
     * @return $this
     */
    public function setTransactionManager($manager)
    {
        $this->transactionsManager = $manager;

        return $this;
    }

    /**
     * Unset the transaction manager for this connection.
     *
     * @return void
     */
    public function unsetTransactionManager()
    {
        $this->transactionsManager = null;
    }

    /**
     * Determine if the connection is in a "dry run".
     *
     * @return bool
     */
    public function pretending()
    {
        return $this->pretending === true;
    }

    /**
     * Get the connection query log.
     *
     * @return array{query: string, bindings: array, time: float|null}[]
     */
    public function getQueryLog()
    {
        return $this->queryLog;
    }

    /**
     * Get the connection query log with embedded bindings.
     *
     * @return array
     */
    public function getRawQueryLog()
    {
        return array_map(fn (array $log) => [
            'raw_query' => $this->queryGrammar->substituteBindingsIntoRawSql(
                $log['query'],
                $this->prepareBindings($log['bindings'])
            ),
            'time' => $log['time'],
        ], $this->getQueryLog());
    }

    /**
     * Clear the query log.
     *
     * @return void
     */
    public function flushQueryLog()
    {
        $this->queryLog = [];
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
     * @return $this
     */
    public function setDatabaseName($database)
    {
        $this->database = $database;

        return $this;
    }

    /**
     * Set the read / write type of the connection.
     *
     * @param  string|null  $readWriteType
     * @return $this
     */
    public function setReadWriteType($readWriteType)
    {
        $this->readWriteType = $readWriteType;

        return $this;
    }

    /**
     * Retrieve the latest read / write type used.
     *
     * @return 'read'|'write'|null
     */
    protected function latestReadWriteTypeUsed()
    {
        return $this->readWriteType ?? $this->latestPdoTypeRetrieved;
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
     * @return $this
     */
    public function setTablePrefix($prefix)
    {
        $this->tablePrefix = $prefix;

        return $this;
    }

    /**
     * Execute the given callback without table prefix.
     *
     * @param  \Closure  $callback
     * @return mixed
     */
    public function withoutTablePrefix(Closure $callback): mixed
    {
        $tablePrefix = $this->getTablePrefix();

        $this->setTablePrefix('');

        try {
            return $callback($this);
        } finally {
            $this->setTablePrefix($tablePrefix);
        }
    }

    /**
     * Get the server version for the connection.
     *
     * @return string
     */
    public function getServerVersion(): string
    {
        return $this->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION);
    }

    /**
     * Register a connection resolver.
     *
     * @param  string  $driver
     * @param  \Closure  $callback
     * @return void
     */
    public static function resolverFor($driver, Closure $callback)
    {
        static::$resolvers[$driver] = $callback;
    }

    /**
     * Get the connection resolver for the given driver.
     *
     * @param  string  $driver
     * @return \Closure|null
     */
    public static function getResolver($driver)
    {
        return static::$resolvers[$driver] ?? null;
    }

    /**
     * Prepare the instance for cloning.
     *
     * @return void
     */
    public function __clone()
    {
        // When cloning, re-initialize grammars to reference cloned connection...
        $this->useDefaultQueryGrammar();

        if (! is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }
    }
}
