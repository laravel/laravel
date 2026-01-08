<?php

use Illuminate\Database\Migrations\Migration;

class CreateTelegramNestedNotificationsTable extends Migration
{
    protected $connection = 'TelegramBot';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        getModuleSchema()->create('nested_notifications', function ($table) {
            $table->bigIncrements('id');
            $table->string('name', 40)->unique();
            $table->longtext('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        getModuleSchema()->dropIfExists('nested_notifications');
    }
}
