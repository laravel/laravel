<?php

use Illuminate\Database\Migrations\Migration;

class CreatePackagesTable extends Migration
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
        getModuleSchema()->create('packages', function ($table) {
            $table->id();

            // Identificadores (Escaneables)
            $table->string('tracking_number')->nullable()->unique(); // Ejemplo: CM980584458AP
            $table->string('awb')->nullable()->unique();             // Ejemplo: 996-13838856
            $table->string('internal_ref')->nullable();              // Ejemplo: 175-24339545

            // Datos del Destinatario (Extraídos de las fotos)
            $table->string('recipient_name');
            $table->string('recipient_id')->nullable(); // Carné de Identidad (ej: 80061220104)
            $table->string('recipient_phone')->nullable();
            $table->text('full_address');
            $table->string('destination_code', 10)->nullable(); // Ejemplo: SCU
            $table->string('province')->nullable();

            // Detalles de la Carga
            $table->string('description')->nullable(); // Ejemplo: GENERADOR DE CORRIENTE
            $table->decimal('weight_kg', 8, 2)->nullable(); // Ejemplo: 11.77 o 14.0
            $table->string('type')->default('no perecedero');
            $table->integer('pieces')->default(1); // PSC: 1

            // Datos del Remitente
            $table->string('sender_name')->nullable();
            $table->string('sender_email')->nullable();

            // Estado y Relaciones
            $table->string('status')->default('received'); // received, in_transit, delivered, etc.
            $table->timestamps();
            $table->softDeletes(); // Recomendado para logística
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
        getModuleSchema()->dropIfExists('packages');
        getModuleSchema()->enableForeignKeyConstraints();
    }
}
