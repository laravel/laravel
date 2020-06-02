<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('option_lists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('option_group_id')->unsigned();
            $table->string('code');
            $table->string('name')->nullable();
            $table->tinyInteger('type')->unsigned()->nullable()->comment('1:加算、2:減算、3:上書き');
            $table->integer('price')->unsigned()->nullable();
            $table->integer('lank')->unsigned()->nullable();
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->bigInteger('deleted_by')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('option_lists');
    }
}
