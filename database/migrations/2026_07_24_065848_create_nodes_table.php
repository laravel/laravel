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
        Schema::create('nodes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('location_x', 10, 6);
            $table->decimal('location_y', 10, 6);
            $table->foreignId('zone_id')->nullable();
            $table->enum('status', ['online', 'offline'])->default('offline');
            $table->timestamp('last_heartbeat')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nodes');
    }
};
