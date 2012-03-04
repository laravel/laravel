<?php namespace Laravel\Database\Schema\Grammars;

use Laravel\Fluent;
use Laravel\Database\Schema\Table;

class MySQL extends Grammar {

	/**
	 * The keyword identifier for the database system.
	 *
	 * @var string
	 */
	public $wrapper = '`%s`';

	/**
	 * Generate the SQL statements for a table creation command.
	 *
	 * @param  Table    $table
	 * @param  Fluent   $command
	 * @return array
	 */
	public function create(Table $table, Fluent $command)
	{
		$columns = implode(', ', $this->columns($table));

		// First we will generate the base table creation statement. Other than auto
		// incrementing keys, no indexes will be created during the first creation
		// of the table as they're added in separate commands.
		$sql = 'CREATE TABLE '.$this->wrap($table).' ('.$columns.')';

		if ( ! is_null($table->engine))
		{
			$sql .= ' ENGINE = '.$table->engine;
		}

		return $sql;
	}

	/**
	 * Geenrate the SQL statements for a table modification command.
	 *
	 * @param  Table   $table
	 * @param  Fluent  $command
	 * @return array
	 */
	public function add(Table $table, Fluent $command)
	{
		$columns = $this->columns($table);

		// Once we the array of column definitions, we need to add "add" to the
		// front of each definition, then we'll concatenate the definitions
		// using commas like normal and generate the SQL.
		$columns = implode(', ', array_map(function($column)
		{
			return 'ADD '.$column;

		}, $columns));

		return 'ALTER TABLE '.$this->wrap($table).' '.$columns;
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
			// types to the correct types.
			$sql = $this->wrap($column).' '.$this->type($column);

			$elements = array('unsigned', 'nullable', 'defaults', 'incrementer');

			foreach ($elements as $element)
			{
				$sql .= $this->$element($table, $column);
			}

			$columns[] = $sql;
		}

		return $columns;
	}

	/**
	 * Get the SQL syntax for indicating if a column is unsigned.
	 *
	 * @param  Table   $table
	 * @param  Fluent  $column
	 * @return string
	 */
	protected function unsigned(Table $table, Fluent $column)
	{
		if ($column->type == 'integer' && $column->unsigned)
		{
			return ' UNSIGNED';
		}
	}

	/**
	 * Get the SQL syntax for indicating if a column is nullable.
	 *
	 * @param  Table   $table
	 * @param  Fluent  $column
	 * @return string
	 */
	protected function nullable(Table $table, Fluent $column)
	{
		return ($column->nullable) ? ' NULL' : ' NOT NULL';
	}

	/**
	 * Get the SQL syntax for specifying a default value on a column.
	 *
	 * @param  Table   $table
	 * @param  Fluent  $column
	 * @return string
	 */
	protected function defaults(Table $table, Fluent $column)
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
	 * @param  Fluent  $column
	 * @return string
	 */
	protected function incrementer(Table $table, Fluent $column)
	{
		if ($column->type == 'integer' and $column->increment)
		{
			return ' AUTO_INCREMENT PRIMARY KEY';
		}
	}

	/**
	 * Generate the SQL statement for creating a primary key.
	 *
	 * @param  Table    $table
	 * @param  Fluent   $command
	 * @return string
	 */
	public function primary(Table $table, Fluent $command)
	{
		return $this->key($table, $command->name(null), 'PRIMARY KEY');
	}

	/**
	 * Generate the SQL statement for creating a unique index.
	 *
	 * @param  Table    $table
	 * @param  Fluent   $command
	 * @return string
	 */
	public function unique(Table $table, Fluent $command)
	{
		return $this->key($table, $command, 'UNIQUE');
	}

	/**
	 * Generate the SQL statement for creating a full-text index.
	 *
	 * @param  Table    $table
	 * @param  Fluent   $command
	 * @return string
	 */
	public function fulltext(Table $table, Fluent $command)
	{
		return $this->key($table, $command, 'FULLTEXT');
	}

	/**
	 * Generate the SQL statement for creating a regular index.
	 *
	 * @param  Table    $table
	 * @param  Fluent   $command
	 * @return string
	 */
	public function index(Table $table, Fluent $command)
	{
		return $this->key($table, $command, 'INDEX');
	}

	/**
	 * Generate the SQL statement for creating a new index.
	 *
	 * @param  Table    $table
	 * @param  Fluent   $command
	 * @param  string   $type
	 * @return string
	 */
	protected function key(Table $table, Fluent $command, $type)
	{
		$keys = $this->columnize($command->columns);

		$name = $command->name;

		return 'ALTER TABLE '.$this->wrap($table)." ADD {$type} {$name}({$keys})";
	}

	/**
	 * Generate the SQL statement for a drop table command.
	 *
	 * @param  Table    $table
	 * @param  Fluent   $command
	 * @return string
	 */
	public function drop(Table $table, Fluent $command)
	{
		return 'DROP TABLE '.$this->wrap($table);
	}

	/**
	 * Generate the SQL statement for a drop column command.
	 *
	 * @param  Table    $table
	 * @param  Fluent   $command
	 * @return string
	 */
	public function drop_column(Table $table, Fluent $command)
	{
		$columns = array_map(array($this, 'wrap'), $command->columns);

		// Once we the array of column names, we need to add "drop" to the front
		// of each column, then we'll concatenate the columns using commas and
		// generate the alter statement SQL.
		$columns = implode(', ', array_map(function($column)
		{
			return 'DROP '.$column;

		}, $columns));

		return 'ALTER TABLE '.$this->wrap($table).' '.$columns;
	}

	/**
	 * Generate the SQL statement for a drop primary key command.
	 *
	 * @param  Table    $table
	 * @param  Fluent   $command
	 * @return string
	 */
	public function drop_primary(Table $table, Fluent $command)
	{
		return 'ALTER TABLE '.$this->wrap($table).' DROP PRIMARY KEY';
	}

	/**
	 * Generate the SQL statement for a drop unqique key command.
	 *
	 * @param  Table    $table
	 * @param  Fluent   $command
	 * @return string
	 */
	public function drop_unique(Table $table, Fluent $command)
	{
		return $this->drop_key($table, $command);
	}

	/**
	 * Generate the SQL statement for a drop full-text key command.
	 *
	 * @param  Table    $table
	 * @param  Fluent   $command
	 * @return string
	 */
	public function drop_fulltext(Table $table, Fluent $command)
	{
		return $this->drop_key($table, $command);
	}

	/**
	 * Generate the SQL statement for a drop unqique key command.
	 *
	 * @param  Table    $table
	 * @param  Fluent   $command
	 * @return string
	 */
	public function drop_index(Table $table, Fluent $command)
	{
		return $this->drop_key($table, $command);
	}

	/**
	 * Generate the SQL statement for a drop key command.
	 *
	 * @param  Table    $table
	 * @param  Fluent   $command
	 * @return string
	 */
	protected function drop_key(Table $table, Fluent $command)
	{
		return 'ALTER TABLE '.$this->wrap($table)." DROP INDEX {$command->name}";
	}

	/**
	 * Drop a foreign key constraint from the table.
	 *
	 * @param  Table   $table
	 * @param  Fluent  $fluent
	 * @return string
	 */
	public function drop_foreign(Table $table, Fluent $command)
	{
		return "ALTER TABLE ".$this->wrap($table)." DROP FOREIGN KEY ".$command->name;
	}

	/**
	 * Generate the data-type definition for a string.
	 *
	 * @param  Fluent   $column
	 * @return string
	 */
	protected function type_string(Fluent $column)
	{
		return 'VARCHAR('.$column->length.')';
	}

	/**
	 * Generate the data-type definition for an integer.
	 *
	 * @param  Fluent  $column
	 * @return string
	 */
	protected function type_integer(Fluent $column)
	{
		return 'INT';
	}

	/**
	 * Generate the data-type definition for an integer.
	 *
	 * @param  Fluent  $column
	 * @return string
	 */
	protected function type_float(Fluent $column)
	{
		return 'FLOAT';
	}

	/**
	 * Generate the data-type definintion for a decimal.
	 *
	 * @param  Fluent  $column
	 * @return string
	 */
	protected function type_decimal(Fluent $column)
	{
		return "DECIMAL({$column->precision}, {$column->scale})";
	}

	/**
	 * Generate the data-type definition for a boolean.
	 *
	 * @param  Fluent  $column
	 * @return string
	 */
	protected function type_boolean(Fluent $column)
	{
		return 'TINYINT';
	}

	/**
	 * Generate the data-type definition for a date.
	 *
	 * @param  Fluent  $column
	 * @return string
	 */
	protected function type_date(Fluent $column)
	{
		return 'DATETIME';
	}

	/**
	 * Generate the data-type definition for a timestamp.
	 *
	 * @param  Fluent  $column
	 * @return string
	 */
	protected function type_timestamp(Fluent $column)
	{
		return 'TIMESTAMP';
	}

	/**
	 * Generate the data-type definition for a text column.
	 *
	 * @param  Fluent  $column
	 * @return string
	 */
	protected function type_text(Fluent $column)
	{
		return 'TEXT';
	}

	/**
	 * Generate the data-type definition for a blob.
	 *
	 * @param  Fluent  $column
	 * @return string
	 */
	protected function type_blob(Fluent $column)
	{
		return 'BLOB';
	}

}