<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function($table) {
            $table->integer('id')->primary();
            $table->string('name');
            $table->softDeletes();
            $table->timestamps();
        });
		Schema::create('posts', function($table) {
            $table->integer('id')->primary();
            $table->integer('user_id')->unsigned();
            $table->string('title');
            $table->timestamps();
        });
		Schema::create('comments', function($table) {
            $table->integer('id')->primary();
            $table->integer('post_id')->unsigned();
            $table->string('title');
            $table->timestamps();
        });
        $user = User::create(['name' => 'Foo Bar']);
        $post = $user->posts()->create(['title' => 'Foo Post']);
        $commet1 = $post->comments()->create(['title' => 'Bar Comment']);
        $commet2 = $post->comments()->create(['title' => 'Baz Comment']);
        $commet = $user->comments()->findOrFail($commet2->getKey());
        $commet_attrs = $commet->getAttributes();
        $commet2_attrs = $commet2->getAttributes();
        ksort($commet_attrs);
        ksort($commet2_attrs);
        if ($commet_attrs != $commet2_attrs)
        {
            print_r($commet2_attrs);
            print_r($commet_attrs);
            throw new Exception("Attributes do not match! SELECT comments.* FROM ... needs to be used instead of SELECT * FROM ...");
        }
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		schema::drop('comments');
        schema::drop('posts');
        schema::drop('usrs');
	}

}
