<?php

use Illuminate\Database\Migrations\Migration;

class CreateProfitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        getModuleSchema()->create('profits', function ($table) {
            $table->bigIncrements('id');
            $table->longtext('name');
            $table->longtext('comment')->nullable();
            $table->text('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        getModuleSchema()->dropIfExists('profits');
    }
}
