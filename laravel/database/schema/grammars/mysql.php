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
	 * Generate the SQL statements for a table creation command.
	 *
	 * @param  Table  $table
	 * @param  array  $command
	 * @return array
	 */
	public function create(Table $table, $command)
	{
		$columns = $this->columns($table);

		// First we will generate the base table creation statement. Other than
		// auto-incrementing keys, no indexes will be created during the first
		// creation of the table. They will be added in separate commands.
		$sql = 'CREATE TABLE '.$this->wrap($table->name).' ('.$columns.')';

		// MySQL supports various "engines" for databas tables. If an engine
		// was specified by the developer, we will set it after adding the
		// columns the table creation statement.
		if ( ! is_null($table->engine))
		{
			$sql .= ' ENGINE = '.$table->engine;
		}

		return (array) $sql;
	}

	/**
	 * Generate the SQL statement for creating a primary key.
	 *
	 * @param  Table   $table
	 * @param  array   $command
	 * @return string
	 */
	public function primary(Table $table, $command)
	{
		return $this->key($table, $command, 'PRIMARY KEY');
	}

	/**
	 * Generate the SQL statement for creating a unique index.
	 *
	 * @param  Table   $table
	 * @param  array   $command
	 * @return string
	 */
	public function unique(Table $table, $command)
	{
		return $this->key($table, $command, 'UNIQUE');
	}

	/**
	 * Generate the SQL statement for creating a full-text index.
	 *
	 * @param  Table   $table
	 * @param  array   $command
	 * @return string
	 */
	public function fulltext(Table $table, $command)
	{
		return $this->key($table, $command, 'FULLTEXT');
	}

	/**
	 * Generate the SQL statement for creating a new index.
	 *
	 * @param  Table   $table
	 * @param  array   $command
	 * @param  string  $type
	 * @return string
	 */
	protected function key(Table $table, $command, $type)
	{
		$keys = $this->columnize($command['columns']);

		return 'ALTER TABLE '.$this->wrap($table->name).' ADD '.$type.' ('.$keys.')';
	}

	protected function columns(Table $table)
	{
		$columns = array();

		foreach ($table->columns as $column)
		{
			$sql = $this->wrap($column->name).' '.$this->type($column);

			$sql .= ($column->nullable) ? ' NULL' : ' NOT NULL';

			if ($column->type() == 'integer' and $column->increment)
			{
				$sql .= ' AUTO_INCREMENT PRIMARY KEY';
			}

			$columns[] = $sql;
		}

		return implode(', ', $columns);
	}

	/**
	 * Generate the data-type definition for a string.
	 *
	 * @param  array   $column
	 * @return string
	 */
	protected function type_string($column)
	{
		return 'VARCHAR('.$column->length.')';
	}

	/**
	 * Generate the data-type definition for an integer.
	 *
	 * @param  Column  $column
	 * @return string
	 */
	protected function type_integer($column)
	{
		return 'INT';
	}

}