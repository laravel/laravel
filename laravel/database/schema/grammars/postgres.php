<?php namespace Laravel\Database\Schema\Grammars;

use Laravel\Database\Schema\Table;
use Laravel\Database\Schema\Columns\Column;
use Laravel\Database\Schema\Commands\Command;

class Postgres extends Grammar {

	/**
	 * Generate the SQL statements for a table creation command.
	 *
	 * @param  Table    $table
	 * @param  Command  $command
	 * @return array
	 */
	public function create(Table $table, Command $command)
	{
		$columns = implode(', ', $this->columns($table));

		// First we will generate the base table creation statement. Other than
		// auto-incrementing keys, no indexes will be created during the first
		// creation of the table. They will be added in separate commands.
		$sql = 'CREATE TABLE '.$this->wrap($table->name).' ('.$columns.')';

		// MySQL supports various "engines" for database tables. If an engine
		// was specified by the developer, we will set it after adding the
		// columns the table creation statement.
		if ( ! is_null($table->engine))
		{
			$sql .= ' ENGINE = '.$table->engine;
		}

		return $sql;
	}

	/**
	 * Geenrate the SQL statements for a table modification command.
	 *
	 * @param  Table    $table
	 * @param  Command  $command
	 * @return array
	 */
	public function add(Table $table, Command $command)
	{
		$columns = $this->columns($table);

		// Once we the array of column definitions, we need to add "add"
		// to the front of each definition, then we'll concatenate the
		// definitions using commas like normal and generate the SQL.
		$columns = implode(', ', array_map(function($column)
		{
			return 'ADD '.$column;

		}, $columns));

		return 'ALTER TABLE '.$this->wrap($table->name).' '.$columns;
	}

	/**
	 * Create the individual column definitions for the table.
	 *
	 * @param  Table  $table
	 * @return array
	 */
	protected function columns(Table $table)
	{
		$columns = array();

		foreach ($table->columns as $column)
		{
			// Each of the data type's have their own definition creation method,
			// which is responsible for creating the SQL for the type. This lets
			// us to keep the syntax easy and fluent, while translating the
			// types to the types used by the database system.
			$sql = $this->wrap($column->name).' '.$this->type($column);

			$elements = array('nullable', 'default_value', 'incrementer');

			foreach ($elements as $element)
			{
				$sql .= $this->$element($table, $column);
			}

			$columns[] = $sql;
		}

		return $columns;
	}

	/**
	 * Get the SQL syntax for indicating if a column is nullable.
	 *
	 * @param  Table   $table
	 * @param  Column  $column
	 * @return string
	 */
	protected function nullable(Table $table, Column $column)
	{
		return ($column->nullable) ? ' NULL' : ' NOT NULL';
	}

	/**
	 * Get the SQL syntax for specifying a default value on a column.
	 *
	 * @param  Table   $table
	 * @param  Column  $column
	 * @return string
	 */
	protected function default_value(Table $table, Column $column)
	{
		if ( ! is_null($column->default))
		{
			return " DEFAULT '".$column->default."'";
		}
	}

	/**
	 * Get the SQL syntax for defining an auto-incrementing column.
	 *
	 * @param  Table   $table
	 * @param  Column  $column
	 * @return string
	 */
	protected function incrementer(Table $table, Column $column)
	{
		if ($column->type() == 'integer' and $column->incremnet)
		{
			return ' AUTO_INCREMENT PRIMARY KEY';
		}
	}

	/**
	 * Generate the SQL statement for creating a primary key.
	 *
	 * @param  Table    $table
	 * @param  Command  $command
	 * @return string
	 */
	public function primary(Table $table, Command $command)
	{
		return $this->key($table, $command, 'PRIMARY KEY');
	}

	/**
	 * Generate the SQL statement for creating a unique index.
	 *
	 * @param  Table    $table
	 * @param  Command  $command
	 * @return string
	 */
	public function unique(Table $table, Command $command)
	{
		return $this->key($table, $command, 'UNIQUE');
	}

	/**
	 * Generate the SQL statement for creating a full-text index.
	 *
	 * @param  Table    $table
	 * @param  Command  $command
	 * @return string
	 */
	public function fulltext(Table $table, Command $command)
	{
		return $this->key($table, $command, 'FULLTEXT');
	}

	/**
	 * Generate the SQL statement for creating a regular index.
	 *
	 * @param  Table    $table
	 * @param  Command  $command
	 * @return string
	 */
	public function index(Table $table, Command $command)
	{
		return $this->key($table, $command, 'INDEX');
	}

	/**
	 * Generate the SQL statement for creating a new index.
	 *
	 * @param  Table    $table
	 * @param  Command  $command
	 * @param  string   $type
	 * @return string
	 */
	protected function key(Table $table, Command $command, $type)
	{
		$keys = $this->columnize($command->columns);

		$name = $command->name;

		return 'ALTER TABLE '.$this->wrap($table->name)." ADD {$type} {$name}({$keys})";
	}

	/**
	 * Generate the SQL statement for a drop table command.
	 *
	 * @param  Table    $table
	 * @param  Command  $command
	 * @return string
	 */
	public function drop(Table $table, Command $command)
	{
		return 'DROP TABLE '.$this->wrap($table->name);
	}

	/**
	 * Generate the SQL statement for a drop column command.
	 *
	 * @param  Table    $table
	 * @param  Command  $command
	 * @return string
	 */
	public function drop_column(Table $table, Command $command)
	{
		$columns = array_map(array($this, 'wrap'), $command->columns);

		// Once we have wrapped all of the columns, we need to add "drop"
		// to the front of each column name, then we'll concatenate the
		// columns using commas like normal and generate the SQL.
		$columns = implode(', ', array_map(function($column)
		{
			return 'DROP '.$column;

		}, $columns));

		return 'ALTER TABLE '.$this->wrap($table->name).' '.$columns;
	}

	/**
	 * Generate the SQL statement for a drop index command.
	 *
	 * @param  Table    $table
	 * @param  Command  $command
	 * @return string
	 */
	public function drop_index(Table $table, Command $command)
	{
		$index = $this->wrap($command->name);

		return 'ALTER TABLE '.$this->wrap($table->name)." DROP INDEX {$index}";
	}

	/**
	 * Generate the data-type definition for a string.
	 *
	 * @param  Column  $column
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

	/**
	 * Generate the data-type definition for an integer.
	 *
	 * @param  Column  $column
	 * @return string
	 */
	protected function type_float($column)
	{
		return 'FLOAT';
	}

	/**
	 * Generate the data-type definition for a boolean.
	 *
	 * @param  Column  $column
	 * @return string
	 */
	protected function type_boolean($column)
	{
		return 'TINYINT';
	}

	/**
	 * Generate the data-type definition for a date.
	 *
	 * @param  Column  $column
	 * @return string
	 */
	protected function type_date($column)
	{
		return 'DATETIME';
	}

	/**
	 * Generate the data-type definition for a text column.
	 *
	 * @param  Column  $column
	 * @return string
	 */
	protected function type_text($column)
	{
		return 'TEXT';
	}

	/**
	 * Generate the data-type definition for a blob.
	 *
	 * @param  Column  $column
	 * @return string
	 */
	protected function type_blob($column)
	{
		return 'BLOB';
	}

}