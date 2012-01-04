<?php namespace Laravel\Database\Schema\Grammars;

use Laravel\Database\Schema\Table;

class MySQL extends Grammar {

	/**
	 * The keyword identifier for the database system.
	 *
	 * @var string
	 */
	public $wrapper = '`';

	/**
	 * Generate the SQL for a table creation command.
	 *
	 * @param  Table   $table
	 * @return string
	 */
	public function create(Table $table)
	{
		$columns = $this->columns($table);

		$sql = 'CREATE TABLE '.$this->wrap($table->name).' ('.$columns.')';

		if ( ! is_null($table->engine))
		{
			$sql .= ' ENGINE = '.$table->engine;
		}

		return $sql;
	}

	protected function columns(Table $table)
	{
		$columns = array();

		foreach ($table->columns as $column)
		{
			$sql = $this->wrap($column->name).' '.$this->type($column);

			$sql .= ($column->nullable) ? ' NULL' : ' NOT NULL';

			if ($column->incrementing)
			{
				$sql .= ' AUTO_INCREMENT';
			}

			die(var_dump(end($columns)));
		}
	}

	protected function type_string($column)
	{
		return 'VARCHAR('.$column->length.')';
	}

}