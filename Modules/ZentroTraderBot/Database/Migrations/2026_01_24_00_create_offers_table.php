<?php

use Illuminate\Database\Migrations\Migration;

class CreateOffersTable extends Migration
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
        getModuleSchema()->create('offers', function ($table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            //$table->foreignId('user_id')->constrained(); // Creador de la oferta
            $table->enum('type', ['buy', 'sell']);      // ¿El usuario quiere comprar o vender USD?
            $table->decimal('amount', 16, 2);           // Cantidad total disponible
            $table->decimal('min_limit', 16, 2);        // Compra mínima (ej: $10)
            $table->decimal('price_per_usd', 16, 2);    // Precio (ej: 1.05 si cobras recargo)
            $table->string('payment_method');           // Zelle, Bizum, Transf. Local
            $table->string('currency')->default('USD');
            $table->enum('status', ['active', 'paused', 'completed', 'cancelled'])->default('active');
            $table->timestamps();

            // Índices para que el bot responda rápido al filtrar
            $table->index(['type', 'status', 'payment_method']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        getModuleSchema()->dropIfExists('offers');
    }
}
