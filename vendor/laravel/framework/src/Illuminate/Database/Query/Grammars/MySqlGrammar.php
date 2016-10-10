<?php namespace Illuminate\Database\Query\Grammars;

use Illuminate\Database\Query\Builder;

class MySqlGrammar extends Grammar {

	/**
	 * The components that make up a select clause.
	 *
	 * @var array
	 */
	protected $selectComponents = array(
		'aggregate',
		'columns',
		'from',
		'joins',
		'wheres',
		'groups',
		'havings',
		'orders',
		'limit',
		'offset',
		'lock',
	);

	/**
	 * Compile a select query into SQL.
	 *
	 * @param  \Illuminate\Database\Query\Builder
	 * @return string
	 */
	public function compileSelect(Builder $query)
	{
		$sql = parent::compileSelect($query);

		if ($query->unions)
		{
			$sql = '('.$sql.') '.$this->compileUnions($query);
		}

		return $sql;
	}

	/**
	 * Compile a single union statement.
	 *
	 * @param  array  $union
	 * @return string
	 */
	protected function compileUnion(array $union)
	{
		$joiner = $union['all'] ? ' union all ' : ' union ';

		return $joiner.'('.$union['query']->toSql().')';
	}

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

		return $value ? 'for update' : 'lock in share mode';
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
		$sql = parent::compileUpdate($query, $values);

		if (isset($query->orders))
		{
			$sql .= ' '.$this->compileOrders($query, $query->orders);
		}

		if (isset($query->limit))
		{
			$sql .= ' '.$this->compileLimit($query, $query->limit);
		}

		return rtrim($sql);
	}

	/**
	 * Wrap a single string in keyword identifiers.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected function wrapValue($value)
	{
		if ($value === '*') return $value;

		return '`'.str_replace('`', '``', $value).'`';
	}

}
