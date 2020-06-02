<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('company_id')->unsigned()->unsigned();
            $table->bigInteger('shop_id')->unsigned();
            $table->tinyInteger('shop_test_flg')->unsigned()->nullable()->default(0);
            $table->tinyInteger('receipt_type')->unsigned()->nullable()
                ->default(0)->comment('0=テイクアウト、1=デリバリー');
            $table->bigInteger('customer_id')->unsigned();
            $table->bigInteger('customer_external_id')->unsigned();
            $table->string('customer_name')->nullable()->comment('利用者漢字性名');
            $table->string('customer_kana')->nullable()->comment('利用者かな性名');
            $table->string('customer_email');
            $table->string('customer_tel');
            $table->integer('total')->nullable();
            $table->integer('shipping_price')->nullable();
            $table->tinyInteger('collection_cargo_flg')->unsigned()
                ->nullable()->default(0)->comment('0=未集荷、1=集荷済');
            $table->integer('payment_total')->nullable();
            $table->tinyInteger('payment_method')->unsigned()
                ->nullable()->comment('1=クレジット、2=現地決済');
            $table->tinyInteger('receipt_flg')->unsigned()
                ->nullable()->default(0)->comment('0=発行しない、1=発行する');
            $table->string('receiver_name')->nullable();
            $table->tinyInteger('receipt_print_flg')->unsigned()->nullable()->default(0);
            $table->dateTime('order_datetime')->nullable();
            $table->dateTime('order_change_datetime')->nullable()->comment('最後に注文を変更した日時');
            $table->dateTime('cancel_datetime')->nullable();
            $table->dateTime('receipt_datetime')->nullable();
            $table->dateTime('receipt_complete_datetime')->nullable();
            $table->dateTime('payment_datetime')->nullable();
            $table->tinyInteger('status')->unsigned()->nullable()
                ->comment('1=仮受注、2=新着、3=準備中、4=未受渡、5=受渡済、6=キャンセル、7=売上確定、8=決済キャンセル、9=キャンセル');
            $table->tinyInteger('settlement_status')->unsigned()
                ->nullable()->comment('0=クレジット以外、1=与信、2=与信キャンセル、3=売上');
            $table->string('settlement_order_id', 30)->nullable();
            $table->bigInteger('seat_id')->nullable();
            $table->integer('receipt_number')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
