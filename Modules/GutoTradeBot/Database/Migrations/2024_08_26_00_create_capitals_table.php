<?php

use Illuminate\Database\Migrations\Migration;

class CreateCapitalsTable extends Migration
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
        getModuleSchema()->create('capitals', function ($table) {
            $table->bigIncrements('id');
            $table->decimal('amount', 10, 2);
            $table->longtext('comment')->nullable();
            $table->text('screenshot');
            $table->bigInteger('sender_id')->nullable(); //->unsigned()
            $table->bigInteger('supervisor_id')->nullable(); //->unsigned()
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
        getModuleSchema()->dropIfExists('capitals');
    }
}
