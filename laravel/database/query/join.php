<?php namespace Laravel\Database\Query;

class Join {

	/**
	 * The type of join being performed.
	 *
	 * @var string
	 */
	public $type;

	/**
	 * The table the join clause is joining to.
	 *
	 * @var string
	 */
	public $table;

	/**
	 * The ON clauses for the join.
	 *
	 * @var array
	 */
	public $clauses = array();

	/**
	 * Create a new query join instance.
	 *
	 * @param  string  $type
	 * @param  string  $table
	 * @return void
	 */
	public function __construct($type, $table)
	{
		$this->type = $type;
		$this->table = $table;
	}

	/**
	 * Add an ON clause to the join.
	 *
	 * @param  string  $column1
	 * @param  string  $operator
	 * @param  string  $column2
	 * @param  string  $connector
	 * @return Join
	 */
	public function on($column1, $operator = null, $column2 = null, $connector = 'AND')
	{
		// If a Closure is passed into the method, it means a nested ON
		// clause is being initiated, so we will take a different course
		// of action than when the statement is just a simple where.
		if (is_callable($column1))
		{
			return $this->on_nested($column1, $connector);
		}

		$this->clauses[] = compact('column1', 'operator', 'column2', 'connector');

		return $this;
	}

	/**
	 * Add an OR ON clause to the join.
	 *
	 * @param  string  $column1
	 * @param  string  $operator
	 * @param  string  $column2
	 * @return Join
	 */
	public function or_on($column1, $operator, $column2)
	{
		return $this->on($column1, $operator, $column2, 'OR');
	}

	/**
	 * Add a nested ON clause to the join.
	 *
	 * @param  Closure  $callback
	 * @param  string   $connector
	 * @return Query
	 */
	public function on_nested($callback, $connector = 'AND')
	{
		$type = 'on_nested';

		// To handle a nested ON clause, we will actually instantiate a new
		// Join instance and run the callback over that instance
		$join = new Join($this->type, $this->table);

		call_user_func($callback, $join);
		
		// Once the callback has been run on the query, we will store the nested
		// query instance on the where clause array so that it's passed to the
		// query's query grammar instance when building.
		if ($join->clauses !== null)
		{
			$this->clauses[] = compact('join', 'connector');
		}

		return $this;
	}

}