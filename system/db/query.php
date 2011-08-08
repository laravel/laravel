<?php namespace System\DB;

use System\Str;
use System\Config;
use System\Paginator;

class Query {

	/**
	 * The database connection.
	 *
	 * @var Connection
	 */
	public $connection;

	/**
	 * The SELECT clause.
	 *
	 * @var string
	 */
	public $select;

	/**
	 * Indicates if the query should return distinct results.
	 *
	 * @var bool
	 */
	public $distinct = false;

	/**
	 * The FROM clause.
	 *
	 * @var string
	 */
	public $from;

	/**
	 * The table name.
	 *
	 * @var string
	 */
	public $table;

	/**
	 * The WHERE clause.
	 *
	 * @var string
	 */
	public $where = 'WHERE 1 = 1';

	/**
	 * The ORDER BY columns.
	 *
	 * @var array
	 */
	public $orderings = array();

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
	 * @param  string      $table
	 * @param  Connection  $connection
	 * @return void
	 */
	public function __construct($table, $connection)
	{
		$this->table = $table;
		$this->connection = $connection;
		$this->from = 'FROM '.$this->wrap($table);
	}

	/**
	 * Create a new query instance.
	 *
	 * @param  string      $table
	 * @param  Connection  $connection
	 * @return Query
	 */
	public static function table($table, $connection)
	{
		return new static($table, $connection);
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
	 * Add columns to the SELECT clause.
	 *
	 * @param  array  $columns
	 * @return Query
	 */
	public function select($columns = array('*'))
	{
		$this->select = ($this->distinct) ? 'SELECT DISTINCT ' : 'SELECT ';

		$this->select .= implode(', ', array_map(array($this, 'wrap'), $columns));

		return $this;
	}

	/**
	 * Set the FROM clause.
	 *
	 * @param  string  $from
	 * @return Query
	 */
	public function from($from)
	{
		$this->from = $from;
		return $this;
	}

	/**
	 * Add a join to the query.
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
		$this->from .= ' '.$type.' JOIN '.$this->wrap($table).' ON '.$this->wrap($column1).' '.$operator.' '.$this->wrap($column2);
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
		$this->where = 'WHERE 1 = 1';
		$this->bindings = array();
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
		$this->where .= ' '.$connector.' '.$where;
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
		$this->where .= ' '.$connector.' '.$this->wrap($column).' '.$operator.' ?';
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
	 * Add a where condition for the primary key to the query.
	 * This is simply a short-cut method for convenience.
	 *
	 * @param  mixed  $value
	 * @return Query
	 */
	public function where_id($value)
	{
		return $this->where('id', '=', $value);
	}

	/**
	 * Add an or where condition for the primary key to the query.
	 * This is simply a short-cut method for convenience.
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
	 * @return Query
	 */
	public function where_in($column, $values, $connector = 'AND')
	{
		$this->where .= ' '.$connector.' '.$this->wrap($column).' IN ('.$this->parameterize($values).')';
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
		$this->where .= ' '.$connector.' '.$this->wrap($column).' NOT IN ('.$this->parameterize($values).')';
		$this->bindings = array_merge($this->bindings, $values);

		return $this;
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
	 * @return Query
	 */
	public function where_null($column, $connector = 'AND')
	{
		$this->where .= ' '.$connector.' '.$this->wrap($column).' IS NULL';
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
		$this->where .= ' '.$connector.' '.$this->wrap($column).' IS NOT NULL';
		return $this;
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

		// The connector variable will determine which connector will be used for the condition.
		// We'll change it as we come across new connectors in the dynamic method string.
		//
		// The index variable helps us get the correct parameter value for the where condition.
		// We increment it each time we add a condition.
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
	public function order_by($column, $direction)
	{
		$this->orderings[] = $this->wrap($column).' '.strtoupper($direction);
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
	 * Set the limit and offset values for a given page.
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
	 * Get an aggregate value.
	 *
	 * @param  string  $aggregate
	 * @param  string  $column
	 * @return mixed
	 */
	private function aggregate($aggregator, $column)
	{
		$this->select = 'SELECT '.$aggregator.'('.$this->wrap($column).') AS '.$this->wrap('aggregate');

		return $this->first()->aggregate;
	}

	/**
	 * Get paginated query results.
	 *
	 * @param  int        $per_page
	 * @param  array      $columns
	 * @return Paginator
	 */
	public function paginate($per_page, $columns = array('*'))
	{
		$total = $this->count();

		return Paginator::make($this->for_page(Paginator::page($total, $per_page), $per_page)->get($columns), $total, $per_page);
	}

	/**
	 * Execute the query as a SELECT statement and return the first result.
	 *
	 * @param  array   $columns
	 * @return object
	 */
	public function first($columns = array('*'))
	{
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
		if (is_null($this->select))
		{
			$this->select($columns);
		}

		$sql = $this->select.' '.$this->from.' '.$this->where;

		if (count($this->orderings) > 0)
		{
			$sql .= ' ORDER BY '.implode(', ', $this->orderings);
		}

		if ( ! is_null($this->limit))
		{
			$sql .= ' LIMIT '.$this->limit;
		}

		if ( ! is_null($this->offset))
		{
			$sql .= ' OFFSET '.$this->offset;
		}

		$results = $this->connection->query($sql, $this->bindings);

		// Reset the SELECT clause so more queries can be performed using the same instance.
		// This is helpful for getting aggregates and then getting actual results.
		$this->select = null;

		return $results;
	}

	/**
	 * Execute an INSERT statement.
	 *
	 * @param  array  $values
	 * @return bool
	 */
	public function insert($values)
	{
		return $this->connection->query($this->compile_insert($values), array_values($values));
	}

	/**
	 * Execute an INSERT statement and get the insert ID.
	 *
	 * @param  array  $values
	 * @return int
	 */
	public function insert_get_id($values)
	{
		$sql = $this->compile_insert($values);

		if ($this->connection->driver() == 'pgsql')
		{
			$query = $this->connection->pdo->prepare($sql.' RETURNING '.$this->wrap('id'));

			$query->execute(array_values($values));

			return $query->fetch(\PDO::FETCH_CLASS, 'stdClass')->id;
		}

		$this->connection->query($sql, array_values($values));

		return $this->connection->pdo->lastInsertId();
	}

	/**
	 * Compile an SQL INSERT statement.
	 *
	 * @param  array   $values
	 * @return string
	 */
	private function compile_insert($values)
	{
		$sql = 'INSERT INTO '.$this->wrap($this->table);

		$columns = array_map(array($this, 'wrap'), array_keys($values));

		return $sql .= ' ('.implode(', ', $columns).') VALUES ('.$this->parameterize($values).')';
	}

	/**
	 * Execute the query as an UPDATE statement.
	 *
	 * @param  array  $values
	 * @return bool
	 */
	public function update($values)
	{
		$sql = 'UPDATE '.$this->wrap($this->table).' SET ';

		foreach (array_keys($values) as $column)
		{
			$sets[] = $this->wrap($column).' = ?';
		}

		return $this->connection->query($sql.implode(', ', $sets).' '.$this->where, array_merge(array_values($values), $this->bindings));
	}

	/**
	 * Execute the query as a DELETE statement.
	 *
	 * @param  int   $id
	 * @return bool
	 */
	public function delete($id = null)
	{
		if ( ! is_null($id)) $this->where('id', '=', $id);

		return $this->connection->query('DELETE FROM '.$this->wrap($this->table).' '.$this->where, $this->bindings);		
	}

	/**
	 * Wrap a value in keyword identifiers.
	 *
	 * @param  string      $value
	 * @return string
	 */
	private function wrap($value)
	{
		if (strpos(strtolower($value), ' as ') !== false)
		{
			return $this->wrap_alias($value);
		}

		$wrap = $this->connection->wrapper();

		foreach (explode('.', $value) as $segment)
		{
			$wrapped[] = ($segment != '*') ? $wrap.$segment.$wrap : $segment;
		}

		return implode('.', $wrapped);
	}

	/**
	 * Wrap an alias in keyword identifiers.
	 *
	 * @param  string      $value
	 * @return string
	 */
	private function wrap_alias($value)
	{
		$segments = explode(' ', $value);

		return $this->wrap($segments[0]).' AS '.$this->wrap($segments[2]);
	}

	/**
	 * Create query parameters from an array of values.
	 *
	 * @param  array  $values
	 * @return string
	 */
	private function parameterize($values)
	{
		return implode(', ', array_fill(0, count($values), '?'));
	}

	/**
	 * Magic Method for handling dynamic functions.
	 */
	public function __call($method, $parameters)
	{
		if (strpos($method, 'where_') === 0)
		{
			return $this->dynamic_where($method, $parameters, $this);
		}

		if (in_array($method, array('count', 'min', 'max', 'avg', 'sum')))
		{
			return ($method == 'count') ? $this->aggregate(strtoupper($method), '*') : $this->aggregate(strtoupper($method), $parameters[0]);
		}

		throw new \Exception("Method [$method] is not defined on the Query class.");
	}

}