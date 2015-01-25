<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Init extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sessions', function($t)
		{
			$t->string('id')->unique();
			$t->text('payload');
			$t->integer('last_activity');
		});

		Schema::create('users', function($t) {
			$t->increments('id');
			$t->string('username', 100);
			$t->string('password', 100);
			$t->string('remember_token', 100)->nullable();
			$t->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sessions');
		Schema::drop('users');
	}

}
