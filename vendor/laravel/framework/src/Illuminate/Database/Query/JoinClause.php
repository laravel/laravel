<?php namespace Illuminate\Database\Query;

class JoinClause {

	/**
	 * The query builder instance.
	 *
	 * @var \Illuminate\Database\Query\Builder
	 */
	public $query;

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
	 * The "on" clauses for the join.
	 *
	 * @var array
	 */
	public $clauses = array();

	/**
	 * Create a new join clause instance.
	 *
	 * @param  \Illuminate\Database\Query\Builder  $query
	 * @param  string  $type
	 * @param  string  $table
	 * @return void
	 */
	public function __construct(Builder $query, $type, $table)
	{
		$this->type = $type;
		$this->query = $query;
		$this->table = $table;
	}

	/**
	 * Add an "on" clause to the join.
	 *
	 * @param  string  $first
	 * @param  string  $operator
	 * @param  string  $second
	 * @param  string  $boolean
	 * @param  bool  $where
	 * @return \Illuminate\Database\Query\JoinClause
	 */
	public function on($first, $operator, $second, $boolean = 'and', $where = false)
	{
		$this->clauses[] = compact('first', 'operator', 'second', 'boolean', 'where');

		if ($where) $this->query->addBinding($second);

		return $this;
	}

	/**
	 * Add an "or on" clause to the join.
	 *
	 * @param  string  $first
	 * @param  string  $operator
	 * @param  string  $second
	 * @return \Illuminate\Database\Query\JoinClause
	 */
	public function orOn($first, $operator, $second)
	{
		return $this->on($first, $operator, $second, 'or');
	}

	/**
	 * Add an "on where" clause to the join.
	 *
	 * @param  string  $first
	 * @param  string  $operator
	 * @param  string  $second
	 * @param  string  $boolean
	 * @return \Illuminate\Database\Query\JoinClause
	 */
	public function where($first, $operator, $second, $boolean = 'and')
	{
		return $this->on($first, $operator, $second, $boolean, true);
	}

	/**
	 * Add an "or on where" clause to the join.
	 *
	 * @param  string  $first
	 * @param  string  $operator
	 * @param  string  $second
	 * @param  string  $boolean
	 * @return \Illuminate\Database\Query\JoinClause
	 */
	public function orWhere($first, $operator, $second)
	{
		return $this->on($first, $operator, $second, 'or', true);
	}

}
