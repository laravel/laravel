<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('cart_id')->unsigned();
            $table->bigInteger('product_id')->unsigned();
            $table->string('product_name')->nullable();
            $table->string('product_code')->nullable();
            $table->integer('price')->nullable();
            $table->integer('option_apply_price')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('subtotal')->nullable()->comment('単価 × 数量');
            $table->integer('payment_subtotal')->nullable()->comment('オプション適用金額を含めた金額');
            $table->integer('provision_time')->nullable();
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
        Schema::dropIfExists('cart_details');
    }
}
