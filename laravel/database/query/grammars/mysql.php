<?php namespace Laravel\Database\Query\Grammars;

class MySQL extends Grammar {

	/**
	 * The keyword identifier for the database system.
	 *
	 * @var string
	 */
	protected $wrapper = '`%s`';
	
	/**
	 * Generates a SQL SHOW columns statement from a Query instance.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	public function show(Query $query)
	{
		$table = $this->wrap_table($query->from);
		$sql = "show columns FROM {$table}";
		return $sql;
	}

}