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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Editor que criou
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->text('resumo')->nullable();
            $table->longText('conteudo');
            $table->string('imagem_destaque')->nullable();
            $table->string('categoria')->nullable(); // Direito Civil, Trabalhista, etc.
            $table->json('tags')->nullable(); // ["contrato", "trabalhista", "indenização"]
            $table->enum('status', ['rascunho', 'publicado', 'arquivado'])->default('rascunho');
            $table->timestamp('published_at')->nullable();
            $table->integer('views')->default(0);
            $table->boolean('is_featured')->default(false); // Post em destaque
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
