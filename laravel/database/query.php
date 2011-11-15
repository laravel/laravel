<?php namespace Laravel\Database; use Laravel\Paginator;

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
	 * @var Grammars\Grammar
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
	 * @param  Connection        $connection
	 * @param  Grammars\Grammar  $grammar
	 * @param  string            $table
	 * @return void
	 */
	public function __construct(Connection $connection, Grammars\Grammar $grammar, $table)
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
	public function join($table, $column1, $operator, $column2, $type = 'INNER')
	{
		$this->joins[] = compact('type', 'table', 'column1', 'operator', 'column2');

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
	public function left_join($table, $column1, $operator, $column2)
	{
		return $this->join($table, $column1, $operator, $column2, 'LEFT');
	}

	/**
	 * Reset the where clause to its initial state. All bindings will be cleared.
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
		$this->wheres[] = array('type' => 'raw', 'connector' => $connector, 'sql' => $where);

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
	public function where($column, $operator, $value, $connector = 'AND')
	{
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
	public function or_where($column, $operator, $value)
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
	 * Add dynamic where conditions to the query.
	 *
	 * Dynamic queries are caught by the __call magic method and are parsed here.
	 * They provide a convenient, expressive API for building simple conditions.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return Query
	 */
	private function dynamic_where($method, $parameters)
	{
		// Strip the "where_" off of the method.
		$finder = substr($method, 6);

		// Split the column names from the connectors.
		$segments = preg_split('/(_and_|_or_)/i', $finder, -1, PREG_SPLIT_DELIM_CAPTURE);

		// The connector variable will determine which connector will be
		// used for the condition. We'll change it as we come across new
		// connectors in the dynamic method string.
		//
		// The index variable helps us get the correct parameter value
		// for the where condition. We increment it each time we add
		// a condition to the query.
		$connector = 'AND';

		$index = 0;

		foreach ($segments as $segment)
		{
			if ($segment != '_and_' and $segment != '_or_')
			{
				$this->where($segment, '=', $parameters[$index], $connector);

				$index++;
			}
			else
			{
				$connector = trim(strtoupper($segment), '_');
			}
		}

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
	 * Set the query limit and offset for a given page and item per page count.
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
		$this->select(array($column));

		return $this->connection->only($this->grammar->select($this), $this->bindings);
	}

	/**
	 * Execute the query as a SELECT statement and return the first result.
	 *
	 * If a single column is selected from the database, only the value of that column will be returned.
	 *
	 * @param  array  $columns
	 * @return mixed
	 */
	public function first($columns = array('*'))
	{
		$columns = (array) $columns;

		return (count($results = $this->take(1)->get($columns)) > 0) ? $results[0] : null;
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

		$results = $this->connection->query($this->grammar->select($this), $this->bindings);

		// Reset the SELECT clause so more queries can be performed using
		// the same instance. This is helpful for getting aggregates and
		// then getting actual results.
		$this->selects = null;

		return $results;
	}

	/**
	 * Get an aggregate value.
	 *
	 * @param  string  $aggregate
	 * @param  string  $column
	 * @return mixed
	 */
	private function aggregate($aggregator, $column)
	{
		$this->aggregate = compact('aggregator', 'column');

		$result = $this->connection->only($this->grammar->select($this), $this->bindings);

		// Reset the aggregate so more queries can be performed using
		// the same instance. This is helpful for getting aggregates
		// and then getting actual results.
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
		// Calculate the current page for the request. The page number
		// will be validated and adjusted by the Paginator class,
		// so we can assume it is valid.
		$page = Paginator::page($total = $this->count(), $per_page);

		return Paginator::make($this->for_page($page, $per_page)->get($columns), $total, $per_page);
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

		foreach ($values as $value)
		{
			$bindings = array_merge($bindings, array_values($value));
		}

		return $this->connection->query($this->grammar->insert($this, $values), $bindings);
	}

	/**
	 * Insert an array of values into the database table and
	 * return the value of the ID column.
	 *
	 * @param  array   $values
	 * @param  string  $sequence
	 * @return int
	 */
	public function insert_get_id($values, $sequence = null)
	{
		$this->connection->query($this->grammar->insert($this, $values), array_values($values));

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
		$value = Manager::raw($this->grammar->wrap($column).$operator.$amount);

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
		$bindings =  array_merge(array_values($values), $this->bindings);

		return $this->connection->query($this->grammar->update($this, $values), $bindings);
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
		if ( ! is_null($id)) $this->where('id', '=', $id);

		return $this->connection->query($this->grammar->delete($this), $this->bindings);		
	}

	/**
	 * Magic Method for handling dynamic functions.
	 *
	 * This method handles all calls to aggregate functions as well
	 * as the construction of dynamic where clauses.
	 */
	public function __call($method, $parameters)
	{
		if (strpos($method, 'where_') === 0)
		{
			return $this->dynamic_where($method, $parameters, $this);
		}

		if (in_array($method, array('abs', 'count', 'min', 'max', 'avg', 'sum')))
		{
			if ($method == 'count')
			{
				return $this->aggregate(strtoupper($method), '*');
			}
			else
			{
				return $this->aggregate(strtoupper($method), $parameters[0]);
			}
		}

		throw new \BadMethodCallException("Method [$method] is not defined on the Query class.");
	}

}
