<?php

use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    protected $connection = 'GutoTradeBot';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        getModuleSchema()->create('comments', function ($table) {
            $table->bigIncrements('id');
            $table->longtext('comment')->nullable();
            $table->text('screenshot')->nullable();
            $table->bigInteger('sender_id');
            $table->bigInteger('payment_id');
            $table->jsonb('data');
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
        getModuleSchema()->dropIfExists('comments');
    }
}
