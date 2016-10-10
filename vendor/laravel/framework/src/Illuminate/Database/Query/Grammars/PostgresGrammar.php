<?php namespace Illuminate\Database\Query\Grammars;

use Illuminate\Database\Query\Builder;

class PostgresGrammar extends Grammar {

	/**
	 * All of the available clause operators.
	 *
	 * @var array
	 */
	protected $operators = array(
		'=', '<', '>', '<=', '>=', '<>', '!=',
		'like', 'not like', 'between', 'ilike',
		'&', '|', '#', '<<', '>>',
	);

	/**
	 * Compile the lock into SQL.
	 *
	 * @param  \Illuminate\Database\Query\Builder  $query
	 * @param  bool|string  $value
	 * @return string
	 */
	protected function compileLock(Builder $query, $value)
	{
		if (is_string($value)) return $value;

		return $value ? 'for update' : 'for share';
	}

	/**
	 * Compile an update statement into SQL.
	 *
	 * @param  \Illuminate\Database\Query\Builder  $query
	 * @param  array  $values
	 * @return string
	 */
	public function compileUpdate(Builder $query, $values)
	{
		$table = $this->wrapTable($query->from);

		// Each one of the columns in the update statements needs to be wrapped in the
		// keyword identifiers, also a place-holder needs to be created for each of
		// the values in the list of bindings so we can make the sets statements.
		$columns = $this->compileUpdateColumns($values);

		$from = $this->compileUpdateFrom($query);

		$where = $this->compileUpdateWheres($query);

		return trim("update {$table} set {$columns}{$from} $where");
	}

	/**
	 * Compile the columns for the update statement.
	 *
	 * @param  array   $values
	 * @return string
	 */
	protected function compileUpdateColumns($values)
	{
		$columns = array();

		// When gathering the columns for an update statement, we'll wrap each of the
		// columns and convert it to a parameter value. Then we will concatenate a
		// list of the columns that can be added into this update query clauses.
		foreach ($values as $key => $value)
		{
			$columns[] = $this->wrap($key).' = '.$this->parameter($value);
		}

		return implode(', ', $columns);
	}

	/**
	 * Compile the "from" clause for an update with a join.
	 *
	 * @param  \Illuminate\Database\Query\Builder  $query
	 * @return string
	 */
	protected function compileUpdateFrom(Builder $query)
	{
		if ( ! isset($query->joins)) return '';

		$froms = array();

		// When using Postgres, updates with joins list the joined tables in the from
		// clause, which is different than other systems like MySQL. Here, we will
		// compile out the tables that are joined and add them to a from clause.
		foreach ($query->joins as $join)
		{
			$froms[] = $this->wrapTable($join->table);
		}

		if (count($froms) > 0) return ' from '.implode(', ', $froms);
	}

	/**
	 * Compile the additional where clauses for updates with joins.
	 *
	 * @param  \Illuminate\Database\Query\Builder  $query
	 * @return string
	 */
	protected function compileUpdateWheres(Builder $query)
	{
		$baseWhere = $this->compileWheres($query);

		if ( ! isset($query->joins)) return $baseWhere;

		// Once we compile the join constraints, we will either use them as the where
		// clause or append them to the existing base where clauses. If we need to
		// strip the leading boolean we will do so when using as the only where.
		$joinWhere = $this->compileUpdateJoinWheres($query);

		if (trim($baseWhere) == '')
		{
			return 'where '.$this->removeLeadingBoolean($joinWhere);
		}
		else
		{
			return $baseWhere.' '.$joinWhere;
		}
	}

	/**
	 * Compile the "join" clauses for an update.
	 *
	 * @param  \Illuminate\Database\Query\Builder  $query
	 * @return string
	 */
	protected function compileUpdateJoinWheres(Builder $query)
	{
		$joinWheres = array();

		// Here we will just loop through all of the join constraints and compile them
		// all out then implode them. This should give us "where" like syntax after
		// everything has been built and then we will join it to the real wheres.
		foreach ($query->joins as $join)
		{
			foreach ($join->clauses as $clause)
			{
				$joinWheres[] = $this->compileJoinConstraint($clause);
			}
		}

		return implode(' ', $joinWheres);
	}

	/**
	 * Compile an insert and get ID statement into SQL.
	 *
	 * @param  \Illuminate\Database\Query\Builder  $query
	 * @param  array   $values
	 * @param  string  $sequence
	 * @return string
	 */
	public function compileInsertGetId(Builder $query, $values, $sequence)
	{
		if (is_null($sequence)) $sequence = 'id';

		return $this->compileInsert($query, $values).' returning '.$this->wrap($sequence);
	}

	/**
	 * Compile a truncate table statement into SQL.
	 *
	 * @param  \Illuminate\Database\Query\Builder  $query
	 * @return array
	 */
	public function compileTruncate(Builder $query)
	{
		return array('truncate '.$this->wrapTable($query->from).' restart identity' => array());
	}

}
