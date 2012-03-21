<?php namespace Laravel\Database;

use Closure;
use Laravel\Database;
use Laravel\Paginator;
use Laravel\Database\Query\Grammars\SQLServer;

class Query {

	/**
	 * The database connection.
	 *
	 * @var Connection
	 */
	public $connection;

	/**
	 * The query grammar instance.
	 *
	 * @var Query\Grammars\Grammar
	 */
	public $grammar;

	/**
	 * The SELECT clause.
	 *
	 * @var array
	 */
	public $selects;

	/**
	 * The aggregating column and function.
	 *
	 * @var array
	 */
	public $aggregate;

	/**
	 * Indicates if the query should return distinct results.
	 *
	 * @var bool
	 */
	public $distinct = false;

	/**
	 * The table name.
	 *
	 * @var string
	 */
	public $from;

	/**
	 * The table joins.
	 *
	 * @var array
	 */
	public $joins;

	/**
	 * The WHERE clauses.
	 *
	 * @var array
	 */
	public $wheres;

	/**
	 * The GROUP BY clauses.
	 *
	 * @var array
	 */
	public $groupings;

	/**
	 * The ORDER BY clauses.
	 *
	 * @var array
	 */
	public $orderings;

	/**
	 * The LIMIT value.
	 *
	 * @var int
	 */
	public $limit;

	/**
	 * The OFFSET value.
	 *
	 * @var int
	 */
	public $offset;

	/**
	 * The query value bindings.
	 *
	 * @var array
	 */
	public $bindings = array();

	/**
	 * Create a new query instance.
	 *
	 * @param  Connection  $connection
	 * @param  Grammar     $grammar
	 * @param  string      $table
	 * @return void
	 */
	public function __construct(Connection $connection, Query\Grammars\Grammar $grammar, $table)
	{
		$this->from = $table;
		$this->grammar = $grammar;
		$this->connection = $connection;
	}

	/**
	 * Force the query to return distinct results.
	 *
	 * @return Query
	 */
	public function distinct()
	{
		$this->distinct = true;
		return $this;
	}

	/**
	 * Add an array of columns to the SELECT clause.
	 *
	 * @param  array  $columns
	 * @return Query
	 */
	public function select($columns = array('*'))
	{
		$this->selects = (array) $columns;
		return $this;
	}

	/**
	 * Add a join clause to the query.
	 *
	 * @param  string  $table
	 * @param  string  $column1
	 * @param  string  $operator
	 * @param  string  $column2
	 * @param  string  $type
	 * @return Query
	 */
	public function join($table, $column1, $operator = null, $column2 = null, $type = 'INNER')
	{
		// If the "column" is really an instance of a Closure, the developer is
		// trying to create a join with a complex "ON" clause. So, we will add
		// the join, and then call the Closure with the join/
		if ($column1 instanceof Closure)
		{
			$this->joins[] = new Query\Join($type, $table);

			call_user_func($column1, end($this->joins));
		}

		// If the column is just a string, we can assume that the join just
		// has a simple on clause, and we'll create the join instance and
		// add the clause automatically for the develoepr.
		else
		{
			$join = new Query\Join($type, $table);

			$join->on($column1, $operator, $column2);

			$this->joins[] = $join;
		}

		return $this;
	}

	/**
	 * Add a left join to the query.
	 *
	 * @param  string  $table
	 * @param  string  $column1
	 * @param  string  $operator
	 * @param  string  $column2
	 * @return Query
	 */
	public function left_join($table, $column1, $operator = null, $column2 = null)
	{
		return $this->join($table, $column1, $operator, $column2, 'LEFT');
	}

	/**
	 * Reset the where clause to its initial state.
	 *
	 * @return void
	 */
	public function reset_where()
	{
		list($this->wheres, $this->bindings) = array(array(), array());
	}

	/**
	 * Add a raw where condition to the query.
	 *
	 * @param  string  $where
	 * @param  array   $bindings
	 * @param  string  $connector
	 * @return Query
	 */
	public function raw_where($where, $bindings = array(), $connector = 'AND')
	{
		$this->wheres[] = array('type' => 'where_raw', 'connector' => $connector, 'sql' => $where);

		$this->bindings = array_merge($this->bindings, $bindings);

		return $this;
	}

	/**
	 * Add a raw or where condition to the query.
	 *
	 * @param  string  $where
	 * @param  array   $bindings
	 * @return Query
	 */
	public function raw_or_where($where, $bindings = array())
	{
		return $this->raw_where($where, $bindings, 'OR');
	}

	/**
	 * Add a where condition to the query.
	 *
	 * @param  string  $column
	 * @param  string  $operator
	 * @param  mixed   $value
	 * @param  string  $connector
	 * @return Query
	 */
	public function where($column, $operator = null, $value = null, $connector = 'AND')
	{
		// If a Closure is passed into the method, it means a nested where
		// clause is being initiated, so we will take a different course
		// of action than when the statement is just a simple where.
		if ($column instanceof Closure)
		{
			return $this->where_nested($column, $connector);
		}

		$type = 'where';

		$this->wheres[] = compact('type', 'column', 'operator', 'value', 'connector');

		$this->bindings[] = $value;

		return $this;
	}

	/**
	 * Add an or where condition to the query.
	 *
	 * @param  string  $column
	 * @param  string  $operator
	 * @param  mixed   $value
	 * @return Query
	 */
	public function or_where($column, $operator = null, $value = null)
	{
		return $this->where($column, $operator, $value, 'OR');
	}

	/**
	 * Add an or where condition for the primary key to the query.
	 *
	 * @param  mixed  $value
	 * @return Query
	 */
	public function or_where_id($value)
	{
		return $this->or_where('id', '=', $value);		
	}

	/**
	 * Add a where in condition to the query.
	 *
	 * @param  string  $column
	 * @param  array   $values
	 * @param  string  $connector
	 * @param  bool    $not
	 * @return Query
	 */
	public function where_in($column, $values, $connector = 'AND', $not = false)
	{
		$type = ($not) ? 'where_not_in' : 'where_in';

		$this->wheres[] = compact('type', 'column', 'values', 'connector');

		$this->bindings = array_merge($this->bindings, $values);

		return $this;
	}

	/**
	 * Add an or where in condition to the query.
	 *
	 * @param  string  $column
	 * @param  array   $values
	 * @return Query
	 */
	public function or_where_in($column, $values)
	{
		return $this->where_in($column, $values, 'OR');
	}

	/**
	 * Add a where not in condition to the query.
	 *
	 * @param  string  $column
	 * @param  array   $values
	 * @param  string  $connector
	 * @return Query
	 */
	public function where_not_in($column, $values, $connector = 'AND')
	{
		return $this->where_in($column, $values, $connector, true);
	}

	/**
	 * Add an or where not in condition to the query.
	 *
	 * @param  string  $column
	 * @param  array   $values
	 * @return Query
	 */
	public function or_where_not_in($column, $values)
	{
		return $this->where_not_in($column, $values, 'OR');
	}

	/**
	 * Add a where null condition to the query.
	 *
	 * @param  string  $column
	 * @param  string  $connector
	 * @param  bool    $not
	 * @return Query
	 */
	public function where_null($column, $connector = 'AND', $not = false)
	{
		$type = ($not) ? 'where_not_null' : 'where_null';

		$this->wheres[] = compact('type', 'column', 'connector');

		return $this;
	}

	/**
	 * Add an or where null condition to the query.
	 *
	 * @param  string  $column
	 * @return Query
	 */
	public function or_where_null($column)
	{
		return $this->where_null($column, 'OR');
	}

	/**
	 * Add a where not null condition to the query.
	 *
	 * @param  string  $column
	 * @param  string  $connector
	 * @return Query
	 */
	public function where_not_null($column, $connector = 'AND')
	{
		return $this->where_null($column, $connector, true);
	}

	/**
	 * Add an or where not null condition to the query.
	 *
	 * @param  string  $column
	 * @return Query
	 */
	public function or_where_not_null($column)
	{
		return $this->where_not_null($column, 'OR');
	}

	/**
	 * Add a nested where condition to the query.
	 *
	 * @param  Closure  $callback
	 * @param  string   $connector
	 * @return Query
	 */
	public function where_nested($callback, $connector = 'AND')
	{
		$type = 'where_nested';

		// To handle a nested where statement, we will actually instantiate a new
		// Query instance and run the callback over that instance, which will
		// allow the developer to have a fresh query instance
		$query = new Query($this->connection, $this->grammar, $this->from);

		call_user_func($callback, $query);

		// Once the callback has been run on the query, we will store the nested
		// query instance on the where clause array so that it's passed to the
		// query's query grammar instance when building.
		$this->wheres[] = compact('type', 'query', 'connector');

		$this->bindings = array_merge($this->bindings, $query->bindings);

		return $this;
	}

	/**
	 * Add dynamic where conditions to the query.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return Query
	 */
	private function dynamic_where($method, $parameters)
	{
		$finder = substr($method, 6);

		$flags = PREG_SPLIT_DELIM_CAPTURE;

		$segments = preg_split('/(_and_|_or_)/i', $finder, -1, $flags);

		// The connector variable will determine which connector will be used
		// for the condition. We'll change it as we come across new boolean
		// connectors in the dynamic method string.
		//
		// The index variable helps us get the correct parameter value for
		// the where condition. We increment it each time we add another
		// condition to the query's where clause.
		$connector = 'AND';

		$index = 0;

		foreach ($segments as $segment)
		{
			// If the segment is not a boolean connector, we can assume it it is
			// a column name, and we'll add it to the query as a new constraint
			// of the query's where clause and keep iterating the segments.
			if ($segment != '_and_' and $segment != '_or_')
			{
				$this->where($segment, '=', $parameters[$index], $connector);

				$index++;
			}
			// Otherwise, we will store the connector so we know how the next
			// where clause we find in the query should be connected to the
			// previous one and will add it when we find the next one.
			else
			{
				$connector = trim(strtoupper($segment), '_');
			}
		}

		return $this;
	}

	/**
	 * Add a grouping to the query.
	 *
	 * @param  string  $column
	 * @return Query
	 */
	public function group_by($column)
	{
		$this->groupings[] = $column;
		return $this;
	}

	/**
	 * Add an ordering to the query.
	 *
	 * @param  string  $column
	 * @param  string  $direction
	 * @return Query
	 */
	public function order_by($column, $direction = 'asc')
	{
		$this->orderings[] = compact('column', 'direction');
		return $this;
	}

	/**
	 * Set the query offset.
	 *
	 * @param  int  $value
	 * @return Query
	 */
	public function skip($value)
	{
		$this->offset = $value;
		return $this;
	}

	/**
	 * Set the query limit.
	 *
	 * @param  int  $value
	 * @return Query
	 */
	public function take($value)
	{
		$this->limit = $value;
		return $this;
	}

	/**
	 * Set the query limit and offset for a given page.
	 *
	 * @param  int    $page
	 * @param  int    $per_page
	 * @return Query
	 */
	public function for_page($page, $per_page)
	{
		return $this->skip(($page - 1) * $per_page)->take($per_page);
	}

	/**
	 * Find a record by the primary key.
	 *
	 * @param  int     $id
	 * @param  array   $columns
	 * @return object
	 */
	public function find($id, $columns = array('*'))
	{
		return $this->where('id', '=', $id)->first($columns);
	}

	/**
	 * Execute the query as a SELECT statement and return a single column.
	 *
	 * @param  string  $column
	 * @return mixed
	 */
	public function only($column)
	{
		$sql = $this->grammar->select($this->select(array($column)));

		return $this->connection->only($sql, $this->bindings);
	}

	/**
	 * Execute the query as a SELECT statement and return the first result.
	 *
	 * @param  array  $columns
	 * @return mixed
	 */
	public function first($columns = array('*'))
	{
		$columns = (array) $columns;

		// Since we only need the first result, we'll go ahead and set the
		// limit clause to 1, since this will be much faster than getting
		// all of the rows and then only returning the first.
		$results = $this->take(1)->get($columns);

		return (count($results) > 0) ? $results[0] : null;
	}

	/**
	 * Get an array with the values of a given column.
	 *
	 * @param  string  $column
	 * @param  string  $key
	 * @return array
	 */
	public function lists($column, $key = null)
	{
		$columns = (is_null($key)) ? array($column) : array($column, $key);

		$results = $this->get($columns);

		// First we will get the array of values for the requested column.
		// Of course, this array will simply have numeric keys. After we
		// have this array we will determine if we need to key the array
		// by another column from the result set.
		$values = array_map(function($row) use ($column)
		{
			return $row->$column;

		}, $results);

		// If a key was provided, we will extract an array of keys and
		// set the keys on the array of values using the array_combine
		// function provided by PHP, which should give us the proper
		// array form to return from the method.
		if ( ! is_null($key))
		{
			return array_combine(array_map(function($row) use ($key)
			{
				return $row->$key;

			}, $results), $values);
		}

		return $values;
	}

	/**
	 * Execute the query as a SELECT statement.
	 *
	 * @param  array  $columns
	 * @return array
	 */
	public function get($columns = array('*'))
	{
		if (is_null($this->selects)) $this->select($columns);

		$sql = $this->grammar->select($this);

		$results = $this->connection->query($sql, $this->bindings);

		// If the query has an offset and we are using the SQL Server grammar,
		// we need to spin through the results and remove the "rownum" from
		// each of the objects since there is no "offset".
		if ($this->offset > 0 and $this->grammar instanceof SQLServer)
		{
			array_walk($results, function($result)
			{
				unset($result->rownum);
			});
		}

		// Reset the SELECT clause so more queries can be performed using
		// the same instance. This is helpful for getting aggregates and
		// then getting actual results from the query.
		$this->selects = null;

		return $results;
	}

	/**
	 * Get an aggregate value.
	 *
	 * @param  string  $aggregator
	 * @param  array   $columns
	 * @return mixed
	 */
	public function aggregate($aggregator, $columns)
	{
		// We'll set the aggregate value so the grammar does not try to compile
		// a SELECT clause on the query. If an aggregator is present, it's own
		// grammar function will be used to build the SQL syntax.
		$this->aggregate = compact('aggregator', 'columns');

		$sql = $this->grammar->select($this);

		$result = $this->connection->only($sql, $this->bindings);

		// Reset the aggregate so more queries can be performed using the same
		// instance. This is helpful for getting aggregates and then getting
		// actual results from the query such as during paging.
		$this->aggregate = null;

		return $result;
	}

	/**
	 * Get the paginated query results as a Paginator instance.
	 *
	 * @param  int        $per_page
	 * @param  array      $columns
	 * @return Paginator
	 */
	public function paginate($per_page = 20, $columns = array('*'))
	{
		// Because some database engines may throw errors if we leave orderings
		// on the query when retrieving the total number of records, we'll drop
		// all of the ordreings and put them back on the query.
		list($orderings, $this->orderings) = array($this->orderings, null);

		$total = $this->count(reset($columns));

		$page = Paginator::page($total, $per_page);

		$this->orderings = $orderings;

		// Now we're ready to get the actual pagination results from the table
		// using the for_page and get methods. The "for_page" method provides
		// a convenient way to set the paging limit and offset.
		$results = $this->for_page($page, $per_page)->get($columns);

		return Paginator::make($results, $total, $per_page);
	}

	/**
	 * Insert an array of values into the database table.
	 *
	 * @param  array  $values
	 * @return bool
	 */
	public function insert($values)
	{
		// Force every insert to be treated like a batch insert to make creating
		// the binding array simpler since we can just spin through the inserted
		// rows as if there/ was more than one every time.
		if ( ! is_array(reset($values))) $values = array($values);

		$bindings = array();

		// We need to merge the the insert values into the array of the query
		// bindings so that they will be bound to the PDO statement when it
		// is executed by the database connection.
		foreach ($values as $value)
		{
			$bindings = array_merge($bindings, array_values($value));
		}

		$sql = $this->grammar->insert($this, $values);

		return $this->connection->query($sql, $bindings);
	}

	/**
	 * Insert an array of values into the database table and return the ID.
	 *
	 * @param  array   $values
	 * @param  string  $sequence
	 * @return int
	 */
	public function insert_get_id($values, $sequence = null)
	{
		$sql = $this->grammar->insert($this, $values);

		$this->connection->query($sql, array_values($values));

		// Some database systems (Postgres) require a sequence name to be
		// given when retrieving the auto-incrementing ID, so we'll pass
		// the given sequence into the method just in case.
		return (int) $this->connection->pdo->lastInsertId($sequence);
	}

	/**
	 * Increment the value of a column by a given amount.
	 *
	 * @param  string  $column
	 * @param  int     $amount
	 * @return int
	 */
	public function increment($column, $amount = 1)
	{
		return $this->adjust($column, $amount, ' + ');
	}

	/**
	 * Decrement the value of a column by a given amount.
	 *
	 * @param  string  $column
	 * @param  int     $amount
	 * @return int
	 */
	public function decrement($column, $amount = 1)
	{
		return $this->adjust($column, $amount, ' - ');
	}

	/**
	 * Adjust the value of a column up or down by a given amount.
	 *
	 * @param  string  $column
	 * @param  int     $amount
	 * @param  string  $operator
	 * @return int
	 */
	protected function adjust($column, $amount, $operator)
	{
		$wrapped = $this->grammar->wrap($column);

		// To make the adjustment to the column, we'll wrap the expression in an
		// Expression instance, which forces the adjustment to be injected into
		// the query as a string instead of bound.
		$value = Database::raw($wrapped.$operator.$amount);

		return $this->update(array($column => $value));
	}

	/**
	 * Update an array of values in the database table.
	 *
	 * @param  array  $values
	 * @return int
	 */
	public function update($values)
	{
		// For update statements, we need to merge the bindings such that the update
		// values occur before the where bindings in the array since the sets will
		// precede any of the where clauses in the SQL syntax that is generated.
		$bindings =  array_merge(array_values($values), $this->bindings);

		$sql = $this->grammar->update($this, $values);

		return $this->connection->query($sql, $bindings);
	}

	/**
	 * Execute the query as a DELETE statement.
	 *
	 * Optionally, an ID may be passed to the method do delete a specific row.
	 *
	 * @param  int   $id
	 * @return int
	 */
	public function delete($id = null)
	{
		// If an ID is given to the method, we'll set the where clause to
		// match on the value of the ID. This allows the developer to
		// quickly delete a row by its primary key value.
		if ( ! is_null($id))
		{
			$this->where('id', '=', $id);
		}

		$sql = $this->grammar->delete($this);

		return $this->connection->query($sql, $this->bindings);		
	}

	/**
	 * Magic Method for handling dynamic functions.
	 *
	 * This method handles calls to aggregates as well as dynamic where clauses.
	 */
	public function __call($method, $parameters)
	{
		if (strpos($method, 'where_') === 0)
		{
			return $this->dynamic_where($method, $parameters, $this);
		}

		// All of the aggregate methods are handled by a single method, so we'll
		// catch them all here and then pass them off to the agregate method
		// instead of creating methods for each one of them.
		if (in_array($method, array('count', 'min', 'max', 'avg', 'sum')))
		{
			if (count($parameters) == 0) $parameters[0] = '*';

			return $this->aggregate(strtoupper($method), (array) $parameters[0]);
		}

		throw new \Exception("Method [$method] is not defined on the Query class.");
	}

}
