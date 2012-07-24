<?php namespace Laravel\Database\Query\Grammars;

class MySQL extends Grammar {

	/**
	 * The keyword identifier for the database system.
	 *
	 * @var string
	 */
	protected $wrapper = '`%s`';

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
            $sql = "SHOW TABLES FROM {$this->connection->config['database']}";
            $results = $this->connection->pdo->query($sql)->fetchAll(\PDO::FETCH_BOTH);

            array_walk($results, function($r) use (&$tables) {
                $tables[] = $r[0];
            });
        }

        return $tables;
    }

}