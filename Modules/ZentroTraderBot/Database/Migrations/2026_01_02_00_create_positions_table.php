<?php

use Illuminate\Database\Migrations\Migration;

class CreatePositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        getModuleSchema()->create('positions', function ($table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('network');      // POL, BNB
            $table->string('pair');         // MATIC/USDT
            $table->string('side');         // LONG (Compra)
            $table->decimal('amount_in', 20, 8);  // Cuanto gastamos (USDT)
            $table->decimal('amount_out', 20, 8); // Cuantos tokens recibimos (POL) - ESTO ES LO QUE VENDEREMOS
            $table->string('tx_hash_open')->nullable();
            $table->string('tx_hash_close')->nullable();
            $table->string('status')->default('OPEN'); // OPEN, CLOSED
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
        getModuleSchema()->dropIfExists('positions');
    }
}
