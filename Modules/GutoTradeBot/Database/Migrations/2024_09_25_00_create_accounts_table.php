<?php

use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        getModuleSchema()->create('accounts', function ($table) {
            $table->bigIncrements('id');
            $table->longtext('bank')->nullable();
            $table->longtext('name');
            $table->longtext('number');
            $table->longtext('detail')->nullable();
            $table->boolean('is_active')->default(true);
            $table->jsonb('data');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        getModuleSchema()->dropIfExists('accounts');
    }
}
