<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('option_groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('source_id')->unsigned()->nullable();
            $table->bigInteger('company_id')->unsigned();
            $table->bigInteger('shop_id')->unsigned()->nullable();
            $table->string('name')->nullable();
            $table->tinyInteger('type')->unsigned()->nullable()->comment('1:選択、2:入力');
            $table->string('unit')->nullable();
            $table->integer('limit')->unsigned()->nullable();
            $table->tinyInteger('public_flg')->unsigned()->nullable();
            $table->tinyInteger('require_flg')->unsigned()->nullable();
            $table->tinyInteger('plural_select_flg')->unsigned()->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->bigInteger('deleted_by')->nullable();
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
        Schema::dropIfExists('option_groups');
    }
}
