<?php namespace Laravel\Database;

use Laravel\IoC;
use Laravel\Str;
use Laravel\Config;
use Laravel\Request;

class Query {

	/**
	 * The database connection.
	 *
	 * @var Connection
	 */
	public $connection;

	/**
	 * The query compiler instance.
	 *
	 * @var Compiler
	 */
	public $compiler;

	/**
	 * The SELECT clause.
	 *
	 * @var array
	 */
	public $select;

	/**
	 * If the query is performing an aggregate function, this will contain the column
	 * and and function to use when aggregating.
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
	public $table;

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
	 * @param  string      $table
	 * @param  Connection  $connection
	 * @param  Compiler    $compiler
	 * @return void
	 */
	public function __construct($table, Connection $connection, Query\Compiler $compiler)
	{
		$this->table = $table;
		$this->compiler = $compiler;
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
	 * <code>
	 *		$query->select(array('id', 'email'));
	 * </code>
	 *
	 * @param  array  $columns
	 * @return Query
	 */
	public function select($columns = array('*'))
	{
		$this->select = $columns;

		return $this;
	}

	/**
	 * Add a join clause to the query.
	 *
	 * <code>
	 *		$query->join('users', 'users.id', '=', 'posts.user_id');
	 * </code>
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
		$this->joins[] = compact('table', 'column1', 'operator', 'column2', 'type');

		return $this;
	}

	/**
	 * Add a left join to the query.
	 *
	 * <code>
	 *		$query->left_join('users', 'users.id', '=', 'posts.user_id');
	 * </code>
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
		$this->wheres = array();

		$this->bindings = array();
	}

	/**
	 * Add a raw where condition to the query.
	 *
	 * <code>
	 *		$query->raw_where('user_id = ? and password = ?', array(1, 'secret'));
	 * </code>
	 *
	 * @param  string  $where
	 * @param  array   $bindings
	 * @param  string  $connector
	 * @return Query
	 */
	public function raw_where($where, $bindings = array(), $connector = 'AND')
	{
		$this->wheres[] = ' '.$connector.' '.$where;

		$this->bindings = array_merge($this->bindings, $bindings);

		return $this;
	}

	/**
	 * Add a raw or where condition to the query.
	 *
	 * <code>
	 *		$query->raw_or_where('user_id = ? and password = ?', array(1, 'secret'));
	 * </code>
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
	 * <code>
	 *		$query->where('id', '=', 1);
	 * </code>
	 *
	 * @param  string  $column
	 * @param  string  $operator
	 * @param  mixed   $value
	 * @param  string  $connector
	 * @return Query
	 */
	public function where($column, $operator, $value, $connector = 'AND')
	{
		$this->wheres[] = array_merge(array('type' => 'where'), compact('column', 'operator', 'value', 'connector'));

		$this->bindings[] = $value;

		return $this;
	}

	/**
	 * Add an or where condition to the query.
	 *
	 * <code>
	 *		$query->or_where('id', '=', 1);
	 * </code>
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
	 *
	 * <code>
	 *		$query->where_id(1);
	 * </code>
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
	 *
	 * <code>
	 *		$query->or_where_id(1);
	 * </code>
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
	 * <code>
	 *		$query->where_in('id', array(1, 2, 3));
	 * </code>
	 *
	 * @param  string  $column
	 * @param  array   $values
	 * @param  string  $connector
	 * @param  bool    $not
	 * @return Query
	 */
	public function where_in($column, $values, $connector = 'AND', $not = false)
	{
		$this->wheres[] = array_merge(array('type' => 'where_in'), compact('column', 'values', 'connector', 'not'));

		$this->bindings = array_merge($this->bindings, $values);

		return $this;
	}

	/**
	 * Add an or where in condition to the query.
	 *
	 * <code>
	 *		$query->or_where_in('id', array(1, 2, 3));
	 * </code>
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
	 * <code>
	 *		$query->where_not_in('id', array(1, 2, 3));
	 * </code>
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
	 * <code>
	 *		$query->or_where_not_in('id', array(1, 2, 3));
	 * </code>
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
		$this->wheres[] = array_merge(array('type' => 'where_null'), compact('column', 'connector', 'not'));

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
	 * <code>
	 *		// Set an ascending sort on the query
	 *		$query->order_by('votes', 'asc');
	 *
	 *		// Set a descending sort on the query
	 *		$query->order_by('votes', 'desc');	 
	 * </code>
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
	 * If the given page is not an integer or is less than zero, one will be used.
	 *
	 * <code>
	 *		// Get the the 15 users that should be displayed for page 1
	 *		$results = DB::table('users')->for_page(1, 15);
	 * </code>
	 *
	 * @param  int    $page
	 * @param  int    $per_page
	 * @return Query
	 */
	public function for_page($page, $per_page)
	{
		if ($page < 1 or filter_var($page, FILTER_VALIDATE_INT) === false) $page = 1;

		return $this->skip(($page - 1) * $per_page)->take($per_page);
	}

	/**
	 * Find a record by the primary key.
	 *
	 * <code>
	 *		// Get the user having an ID of 1
	 *		$user = DB::table('users')->find(1);
	 * </code>
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
		$this->aggregate = compact('aggregator', 'column');

		$result = $this->connection->scalar($this->compiler->select($this), $this->bindings);

		// Reset the SELECT clause so more queries can be performed using the same instance.
		// This is helpful for getting aggregates and then getting actual results.
		$this->select = null;

		return $result;
	}

	/**
	 * Execute the query as a SELECT statement and return the first result.
	 *
	 * @param  array     $columns
	 * @return stdClass
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
		if (is_null($this->select)) $this->select($columns);

		$results = $this->connection->query($this->compiler->select($this), $this->bindings);

		// Reset the SELECT clause so more queries can be performed using the same instance.
		// This is helpful for getting aggregates and then getting actual results.
		$this->select = null;

		return $results;
	}

	/**
	 * Insert an array of values into the database table.
	 *
	 * <code>
	 *		// Insert into the "users" table
	 *		$success = DB::table('users')->insert(array('id' => 1, 'email' => 'example@gmail.com'));
	 * </code>
	 *
	 * @param  array  $values
	 * @return bool
	 */
	public function insert($values)
	{
		return $this->connection->query($this->compiler->insert($this, $values), array_values($values));
	}

	/**
	 * Insert an array of values into the database table and return the value of the ID column.
	 *
	 * <code>
	 *		// Insert into the "users" table and get the auto-incrementing ID
	 *		$id = DB::table('users')->insert_get_id(array('email' => 'example@gmail.com'));
	 * </code>
	 *
	 * @param  array  $values
	 * @return int
	 */
	public function insert_get_id($values)
	{
		$this->connection->query($this->compiler->insert($this, $values), array_values($values));

		return (int) $this->connection->pdo->lastInsertId();
	}

	/**
	 * Update an array of values in the database table.
	 *
	 * <code>
	 *		// Update a user's e-mail address
	 *		$affected = DB::table('users')->where_id(1)->update(array('email' => 'new_email@example.com'));
	 * </code>
	 *
	 * @param  array  $values
	 * @return int
	 */
	public function update($values)
	{
		return $this->connection->query($this->compiler->update($this, $values), array_merge(array_values($values), $this->bindings));
	}

	/**
	 * Execute the query as a DELETE statement.
	 *
	 * Optionally, an ID may be passed to the method do delete a specific row.
	 *
	 * <code>
	 *		// Delete everything from the "users" table
	 *		$affected = DB::table('users')->delete();
	 *
	 *		// Delete a specific user from the table
	 *		$affected = DB::table('users')->delete(1);
	 *
	 *		// Execute a delete statement with where conditions
	 *		$affected = DB::table('users')->where_email($email)->delete();
	 * </code>
	 *
	 * @param  int   $id
	 * @return int
	 */
	public function delete($id = null)
	{
		if ( ! is_null($id)) $this->where('id', '=', $id);

		return $this->connection->query($this->compiler->delete($this), $this->bindings);		
	}

	/**
	 * Magic Method for handling dynamic functions.
	 *
	 * This method handles all calls to aggregate functions as well as the construction of dynamic where clauses.
	 *
	 * <code>
	 *		// Get the total number of rows on the "users" table
	 *		$count = DB::table('users')->count();
	 *
	 *		// Get a user using a dynamic where clause
	 *		$user = DB::table('users')->where_email('example@gmail.com')->first();
	 * </code>
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