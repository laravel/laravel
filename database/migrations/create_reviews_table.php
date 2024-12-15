<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('coffee_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->integer('rating')->default(5); // 1-5 stars
            $table->text('comment')->nullable();
            $table->timestamps();
            // Prevent multiple reviews for same order
            $table->unique(['order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
}; 