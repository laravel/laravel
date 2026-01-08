<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActorsTable extends Migration
{
    protected $connection = 'TelegramBot';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        getModuleSchema()->create('actors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id'); //->unsigned()
            $table->jsonb('data');

            //$table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        getModuleSchema()->dropIfExists('actors');
    }
}
