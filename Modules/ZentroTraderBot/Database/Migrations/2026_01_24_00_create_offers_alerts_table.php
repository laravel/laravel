<?php

use Illuminate\Database\Migrations\Migration;

class CreateOffersAlertsTable extends Migration
{
    protected $connection = 'ZentroTraderBot';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        getModuleSchema()->create('offers_alerts', function ($table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            //$table->foreignId('user_id')->constrained(); // Creador de la oferta
            $table->enum('type', ['buy', 'sell']);
            $table->string('payment_method')->nullable();
            $table->decimal('max_price', 16, 2)->nullable(); // "Avísame si alguien vende a menos de 1.02"
            $table->boolean('is_active')->default(true);
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
        getModuleSchema()->dropIfExists('offers_alerts');
    }
}
