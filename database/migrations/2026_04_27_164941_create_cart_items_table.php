<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cart is stored in DB (not just localStorage) so it's persistent per user
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity', 10, 2)->default(1); // in kg
            $table->timestamps();

            $table->unique(['user_id', 'product_id']); // one row per product per user
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};