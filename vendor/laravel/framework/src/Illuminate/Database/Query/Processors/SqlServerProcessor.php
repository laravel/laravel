<?php namespace Illuminate\Database\Query\Processors;

use Illuminate\Database\Query\Builder;

class SqlServerProcessor extends Processor {

	/**
	 * Process an "insert get ID" query.
	 *
	 * @param  \Illuminate\Database\Query\Builder  $query
	 * @param  string  $sql
	 * @param  array   $values
	 * @param  string  $sequence
	 * @return int
	 */
	public function processInsertGetId(Builder $query, $sql, $values, $sequence = null)
	{
		$query->getConnection()->insert($sql, $values);

		$id = $query->getConnection()->getPdo()->lastInsertId();

		return is_numeric($id) ? (int) $id : $id;
	}

	/**
	 * Process the results of a column listing query.
	 *
	 * @param  array  $results
	 * @return array
	 */
	public function processColumnListing($results)
	{
		return array_values(array_map(function($r) { return $r->name; }, $results));
	}

}
