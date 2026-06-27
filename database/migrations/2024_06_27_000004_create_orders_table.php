<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('target_id');
            $table->string('target_zone')->nullable();
            $table->enum('status', ['pending', 'paid', 'processing', 'success', 'failed', 'expired'])->default('pending');
            $table->string('payment_ref')->nullable();
            $table->string('supplier_ref')->nullable();
            $table->integer('amount');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
