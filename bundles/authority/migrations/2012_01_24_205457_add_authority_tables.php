<?php

class Authority_add_authority_tables {

	public function up()
	{
		Schema::table('users', function($table)
		{
			$table->create();
			$table->increments('id');
			$table->string('email');
			$table->string('password');
			$table->string('name');
			$table->timestamp('created_at');
			$table->timestamp('updated_at');
		});

		Schema::table('roles', function($table)
		{
			$table->create();
			$table->increments('id');
			$table->string('key');
		});

		Schema::table('roles_users', function($table)
		{
			$table->create();
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('role_id');
		});
	}

	public function down()
	{
		Schema::table('users', function($table)
		{
			$table->drop();
		});

		Schema::table('roles', function($table)
		{
			$table->drop();
		});

		Schema::table('roles_users', function($table)
		{
			$table->drop();
		});
	}

}