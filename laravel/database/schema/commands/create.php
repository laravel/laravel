<?php namespace Laravel\Database\Schema\Commands;

use Laravel\Database as DB;
use Laravel\Database\Schema\Table;
use Laravel\Database\Schema\Grammars\Factory as Grammar;

class Create {

	/**
	 * The schema table instance for the command.
	 *
	 * @var Table
	 */
	public $table;

	/**
	 * Create a new table creation schema command.
	 *
	 * @param  Table  $table
	 * @return void
	 */
	public function __construct(Table $table)
	{
		$this->table = $table;
	}

	/**
	 * Execute the table creation command.
	 *
	 * @return void
	 */
	public function execute()
	{
		//$connection = DB::connection($table->connection);

		$grammar = Grammar::make('mysql');

		$sql = $grammar->create($this->table);

		die($sql);

		$connection->query($grammar->create($this->table));
	}

}