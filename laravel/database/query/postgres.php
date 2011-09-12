<?php namespace Laravel\Database\Query;

use PDO;

class Postgres extends Query {

	/**
	 * Insert an array of values into the database table and return the value of the ID column.
	 *
	 * @param  array  $values
	 * @return int
	 */
	public function insert_get_id($values)
	{
		$query = $this->connection->pdo->prepare($this->compiler->insert_get_id($this, $values));

		$query->execute(array_values($values));

		return (int) $query->fetch(PDO::FETCH_CLASS, 'stdClass')->id;
	}

}