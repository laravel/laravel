<?php

use Illuminate\Database\Migrations\Migration;

class CreateHistoriesTable extends Migration
{
    protected $connection = 'ZentroPackageBot';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        getModuleSchema()->create('histories', function ($table) {
            $table->id();

            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->string('status'); // El nuevo estado
            $table->string('location')->nullable(); // Ciudad o coordenadas GPS si el bot las capturó
            $table->text('comment')->nullable(); // "Escaneado vía Telegram"
            $table->foreignId('user_id')->nullable(); // El ID del mensajero que escaneó
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
        getModuleSchema()->disableForeignKeyConstraints();
        getModuleSchema()->dropIfExists('histories');
        getModuleSchema()->enableForeignKeyConstraints();
    }
}
