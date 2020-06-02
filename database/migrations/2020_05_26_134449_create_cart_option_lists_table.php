<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartOptionListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_option_lists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('cart_detail_id')->unsigned();
            $table->bigInteger('option_group_id')->unsigned()->nullable();
            $table->string('option_group_name')->nullable();
            $table->string('option_group_unit')->nullable();
            $table->integer('option_group_input')->unsigned()->nullable();
            $table->bigInteger('option_list_id')->unsigned()->nullable();
            $table->string('option_list_code')->nullable();
            $table->string('option_list_name')->nullable();
            $table->tinyInteger('option_list_type')->unsigned()->nullable()->comment('1:加算、2:減算');
            $table->integer('option_list_price')->unsigned()->nullable();
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_option_lists');
    }
}
