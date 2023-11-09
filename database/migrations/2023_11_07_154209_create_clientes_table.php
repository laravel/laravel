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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId(column: 'user_id')->constrained(); 
            $table->string(column: 'nome'); 
            $table->string(column: 'email'); 
            $table->string(column: 'telefone'); 
            $table->string(column: 'empresa'); 
            $table->string(column: 'tel_comercial'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
