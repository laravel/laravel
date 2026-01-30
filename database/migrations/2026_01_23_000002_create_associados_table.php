<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('associados', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cargo'); // Sócio, Advogado Associado, Estagiário, etc.
            $table->string('oab')->nullable(); // Número da OAB
            $table->string('foto')->nullable();
            $table->text('bio')->nullable();
            $table->string('email')->nullable();
            $table->string('telefone')->nullable();
            $table->string('linkedin')->nullable();
            $table->json('areas_atuacao')->nullable(); // ["Direito Civil", "Direito Trabalhista"]
            $table->integer('ordem')->default(0); // Para ordenação na página
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('associados');
    }
};
