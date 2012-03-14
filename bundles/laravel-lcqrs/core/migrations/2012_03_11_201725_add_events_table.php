<?php

class LCQRS_Add_Events_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('events', function($table)
		{
			$table->increments('id');
			$table->string('aggregateroot_uuid')->index();
			$table->string('aggregateroot_name');
			$table->text('event');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('events');
	}

}