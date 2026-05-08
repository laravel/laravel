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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Customer info snapshot at time of order (editable before placing)
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('customer_address');

            $table->date('pickup_date');
            $table->time('pickup_time')->nullable();

            $table->decimal('total_amount', 10, 2)->default(0);

            // Status: pending | confirmed | completed | cancelled
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');

            $table->text('notes')->nullable(); // customer notes
            $table->text('admin_notes')->nullable(); // admin notes

            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};