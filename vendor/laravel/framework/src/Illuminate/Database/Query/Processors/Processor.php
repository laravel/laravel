<?php namespace Illuminate\Database\Query\Processors;

use Illuminate\Database\Query\Builder;

class Processor {

	/**
	 * Process the results of a "select" query.
	 *
	 * @param  \Illuminate\Database\Query\Builder  $query
	 * @param  array  $results
	 * @return array
	 */
	public function processSelect(Builder $query, $results)
	{
		return $results;
	}

	/**
	 * Process an  "insert get ID" query.
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

		$id = $query->getConnection()->getPdo()->lastInsertId($sequence);

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
		return $results;
	}

}
