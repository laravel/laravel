<?php namespace Laravel\Database\Query\Grammars;

use Laravel\Database\Query;

class Postgres extends Grammar {

	/**
	 * Compile a SQL INSERT and get ID statment from a Query instance.
	 *
	 * @param  Query   $query
	 * @param  array   $values
	 * @param  string  $column
	 * @return string
	 */
	public function insert_get_id(Query $query, $values, $column)
	{
		return $this->insert($query, $values)." RETURNING $column";
	}

    /**
     * Get an array of tables
     *
     * @return array
     */
    public function tables()
    {
        $tables = array();

        if ($this->connection instanceof \Laravel\Database\Connection)
        {
            $schema = isset($this->connection->config['schema']) ? $this->connection->config['schema'] : 'public';
            $sql = "SELECT table_name AS name FROM INFORMATION_SCHEMA.tables WHERE table_schema = '{$schema}'";
            $results = $this->connection->query($sql);

            array_walk($results, function($r) use (&$tables) {
                $tables[] = $r->name;
            });
        }

        return $tables;
    }

}