<?php

class Create_Session_Table {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table(Config::get('session.table'), function($table)
		{
			$table->create();

			// The session table consists simply of an ID, a UNIX timestamp to
			// indicate the expiration time, and a blob field which will hold
			// the serialized form of the session payload.
			$table->string('id')->length(40)->primary('session_primary');

			$table->integer('last_activity');

			$table->text('data');
		});
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table(Config::get('session.table'), function($table)
		{
			$table->drop();
		});
	}

}