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
     * Get an array of tables
     *
     * @return array
     */
    public function tables()
    {
        $tables = array();

        if ($this->connection instanceof \Laravel\Database\Connection)
        {
            $sql = "SELECT name FROM sqlite_master WHERE type='table' ORDER BY name";
            $results = $this->connection->query($sql);

            array_walk($results, function($r) use (&$tables) {
                $tables[] = $r->name;
            });
        }

        return $tables;
    }

}