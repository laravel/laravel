<?php namespace Illuminate\Database\Query\Grammars;

use Illuminate\Database\Query\Builder;

class SQLiteGrammar extends Grammar {

	/**
	 * All of the available clause operators.
	 *
	 * @var array
	 */
	protected $operators = array(
		'=', '<', '>', '<=', '>=', '<>', '!=',
		'like', 'not like', 'between', 'ilike',
		'&', '|', '<<', '>>',
	);

	/**
	 * Compile an insert statement into SQL.
	 *
	 * @param  \Illuminate\Database\Query\Builder  $query
	 * @param  array  $values
	 * @return string
	 */
	public function compileInsert(Builder $query, array $values)
	{
		// Essentially we will force every insert to be treated as a batch insert which
		// simply makes creating the SQL easier for us since we can utilize the same
		// basic routine regardless of an amount of records given to us to insert.
		$table = $this->wrapTable($query->from);

		if ( ! is_array(reset($values)))
		{
			$values = array($values);
		}

		// If there is only one record being inserted, we will just use the usual query
		// grammar insert builder because no special syntax is needed for the single
		// row inserts in SQLite. However, if there are multiples, we'll continue.
		if (count($values) == 1)
		{
			return parent::compileInsert($query, reset($values));
		}

		$names = $this->columnize(array_keys(reset($values)));

		$columns = array();

		// SQLite requires us to build the multi-row insert as a listing of select with
		// unions joining them together. So we'll build out this list of columns and
		// then join them all together with select unions to complete the queries.
		foreach (array_keys(reset($values)) as $column)
		{
			$columns[] = '? as '.$this->wrap($column);
		}

		$columns = array_fill(0, count($values), implode(', ', $columns));

		return "insert into $table ($names) select ".implode(' union select ', $columns);
	}

	/**
	 * Compile a truncate table statement into SQL.
	 *
	 * @param  \Illuminate\Database\Query\Builder  $query
	 * @return array
	 */
	public function compileTruncate(Builder $query)
	{
		$sql = array('delete from sqlite_sequence where name = ?' => array($query->from));

		$sql['delete from '.$this->wrapTable($query->from)] = array();

		return $sql;
	}

	/**
	 * Compile a "where day" clause.
	 *
	 * @param  \Illuminate\Database\Query\Builder  $query
	 * @param  array  $where
	 * @return string
	 */
	protected function whereDay(Builder $query, $where)
	{
		return $this->dateBasedWhere('%d', $query, $where);
	}

	/**
	 * Compile a "where month" clause.
	 *
	 * @param  \Illuminate\Database\Query\Builder  $query
	 * @param  array  $where
	 * @return string
	 */
	protected function whereMonth(Builder $query, $where)
	{
		return $this->dateBasedWhere('%m', $query, $where);
	}

	/**
	 * Compile a "where year" clause.
	 *
	 * @param  \Illuminate\Database\Query\Builder  $query
	 * @param  array  $where
	 * @return string
	 */
	protected function whereYear(Builder $query, $where)
	{
		return $this->dateBasedWhere('%Y', $query, $where);
	}

	/**
	 * Compile a date based where clause.
	 *
	 * @param  string  $type
	 * @param  \Illuminate\Database\Query\Builder  $query
	 * @param  array  $where
	 * @return string
	 */
	protected function dateBasedWhere($type, Builder $query, $where)
	{
		$value = str_pad($where['value'], 2, '0', STR_PAD_LEFT);

		$value = $this->parameter($value);

		return 'strftime(\''.$type.'\', '.$this->wrap($where['column']).') '.$where['operator'].' '.$value;
	}

}
