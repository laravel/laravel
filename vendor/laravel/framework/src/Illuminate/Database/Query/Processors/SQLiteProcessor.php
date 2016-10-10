<?php namespace Illuminate\Database\Query\Processors;

class SQLiteProcessor extends Processor {

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
