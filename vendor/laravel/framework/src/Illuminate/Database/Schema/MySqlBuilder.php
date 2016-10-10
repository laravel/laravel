<?php namespace Illuminate\Database\Schema;

class MySqlBuilder extends Builder {

	/**
	 * Determine if the given table exists.
	 *
	 * @param  string  $table
	 * @return bool
	 */
	public function hasTable($table)
	{
		$sql = $this->grammar->compileTableExists();

		$database = $this->connection->getDatabaseName();

		$table = $this->connection->getTablePrefix().$table;

		return count($this->connection->select($sql, array($database, $table))) > 0;
	}

	/**
	 * Get the column listing for a given table.
	 *
	 * @param  string  $table
	 * @return array
	 */
	public function getColumnListing($table)
	{
		$sql = $this->grammar->compileColumnExists();

		$database = $this->connection->getDatabaseName();

		$table = $this->connection->getTablePrefix().$table;

		$results = $this->connection->select($sql, array($database, $table));

		return $this->connection->getPostProcessor()->processColumnListing($results);
	}

}
