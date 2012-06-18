<?php

class {{class}} {
	
	private $table = '';

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		/*
		Schema::create($this->table, function($table) {
			$table->increments('id');
			$table->timestamps();
		});
		
		DB::table($this->table)->insert(Array(
			//
		));
		*/
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		/*
		Schema::drop($this->table);
		*/
	}

}