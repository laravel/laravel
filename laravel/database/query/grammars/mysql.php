<?php 
namespace Laravel\Database\Query\Grammars;
use Laravel\Database\Query;

class MySQL extends Grammar {

	/**
	 * The keyword identifier for the database system.
	 *
	 * @var string
	 */
	protected $wrapper = '`%s`';

	/**
	 * Compile a SQL UPDATE statement from a Query instance.
	 *
	 * @param  Query   $query
	 * @param  array   $values
	 * @return string
	 */
	public function update(Query $query, $values)
	{
		$table = $this->wrap_table($query->from);

		// Each column in the UPDATE statement needs to be wrapped in the keyword
		// identifiers, and a place-holder needs to be created for each value in
		// the array of bindings, so we'll build the sets first.
		foreach ($values as $column => $value)
		{
			$columns[] = $this->wrap($column).' = '.$this->parameter($value);
		}

		$columns = implode(', ', $columns);

		$components = $this->components($query);

		$limit  = isset($components['limit']) ? " {$components['limit']}" : '';
		$orderings  = isset($components['orderings']) ? " {$components['orderings']}" : '';

		// UPDATE statements may be constrained by a WHERE clause, so we'll run
		// the entire where compilation process for those constraints. This is
		// easily achieved by passing it to the "wheres" method.
		//dd(trim("UPDATE {$table} SET {$columns} ".$this->wheres($query)).$limit);
		return trim("UPDATE {$table} SET {$columns} ".$this->wheres($query)).$orderings.$limit;
	}

	/**
	 * Compile a SQL DELETE statement from a Query instance.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	public function delete(Query $query)
	{
		$table = $this->wrap_table($query->from);

		$components = $this->components($query);

		$limit  = isset($components['limit']) ? " {$components['limit']}" : '';
		$orderings  = isset($components['orderings']) ? " {$components['orderings']}" : '';

		return trim("DELETE FROM {$table} ".$this->wheres($query)).$orderings.$limit;
	}

}
