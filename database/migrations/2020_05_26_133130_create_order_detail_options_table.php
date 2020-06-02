<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_detail_options', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->bigInteger('order_detail_id')->unsigned();
            $table->bigInteger('option_group_id')->unsigned()->nullable();
            $table->string('option_group_name')->nullable();
            $table->string('option_group_unit')->nullable();
            $table->unsignedInteger('option_group_input')->nullable();
            $table->bigInteger('option_list_id')->unsigned()->nullable();
            $table->string('option_list_code')->nullable();
            $table->string('option_list_name')->nullable();
            $table->tinyInteger('option_list_type')->nullable()->comment("1:加算、2:減算");
            $table->unsignedInteger('option_list_price')->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->bigInteger('created_by')->unsigned()->nullable();
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
        Schema::dropIfExists('order_detail_options');
    }
}
