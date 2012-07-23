<?php namespace Laravel\Database\Query\Grammars;

use Laravel\Database\Query;

class SQLite extends Grammar
{

	/**
	 * Compile the ORDER BY clause for a query.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	protected function orderings(Query $query)
	{
		foreach ($query->orderings as $ordering)
		{
			$sql[] = $this->wrap($ordering['column']).' COLLATE NOCASE '.strtoupper($ordering['direction']);
		}

		return 'ORDER BY '.implode(', ', $sql);
	}

	/**
	 * Returns the SQL to get the name and type of each column. Note that all info
	 * is always returned for SQLite, regardless of the $all_info flag.
	 *
	 * @param  Query   $query
	 * @param  bool    $all_info
	 * @return string
	 */
	public function columns(Query $query, $all_info = false)
	{
		return "PRAGMA table_info({$query->from})";
	}

}