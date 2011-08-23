<?php namespace Laravel\DB\Query;

use Laravel\DB\Query;

class Postgres extends Query {

	/**
	 * Insert an array of values into the database table and return the value of the ID column.
	 *
	 * <code>
	 *		// Insert into the "users" table and get the auto-incrementing ID
	 *		$id = DB::table('users')->insert_get_id(array('email' => 'example@gmail.com'));
	 * </code>
	 *
	 * @param  array  $values
	 * @return int
	 */
	public function insert_get_id($values)
	{
		$query = $this->connection->pdo->prepare($this->compiler->insert_get_id($this, $values));

		$query->execute(array_values($values));

		return (int) $query->fetch(\PDO::FETCH_CLASS, 'stdClass')->id;
	}

}