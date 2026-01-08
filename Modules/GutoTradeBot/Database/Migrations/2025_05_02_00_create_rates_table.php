<?php

use Illuminate\Database\Migrations\Migration;

class CreateRatesTable extends Migration
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
        getModuleSchema()->create('rates', function ($table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->string('base')->default('tether');
            $table->string('coin');
            $table->decimal('rate', 20, 10);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        getModuleSchema()->dropIfExists('rates');
    }
}
