<?php

use Illuminate\Database\Migrations\Migration;

class CreatePenaltiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        getModuleSchema()->create('penalties', function ($table) {
            $table->bigIncrements('id');
            $table->decimal('from', 10, 2);
            $table->decimal('to', 10, 2);
            $table->bigInteger('amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        getModuleSchema()->dropIfExists('penalties');
    }
}
