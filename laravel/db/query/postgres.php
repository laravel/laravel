<?php namespace Laravel\DB\Query;

use Laravel\DB\Query;

class Postgres extends Query {

	/**
	 * Execute an INSERT statement and get the insert ID.
	 *
	 * @param  array  $values
	 * @return int
	 */
	public function insert_get_id($values)
	{
		$sql = $this->compile_insert($values);

		$query = $this->connection->pdo->prepare($sql.' RETURNING '.$this->wrap('id'));

		$query->execute(array_values($values));

		return $query->fetch(\PDO::FETCH_CLASS, 'stdClass')->id;
	}

}