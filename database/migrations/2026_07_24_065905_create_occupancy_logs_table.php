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
        Schema::create('occupancy_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id');
            $table->foreignId('node_id');
            $table->foreignId('zone_id');
            $table->enum('action', ['enter', 'exit']);
            $table->enum('method', ['rfid', 'ble']);
            $table->timestamp('timestamp');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('occupancy_logs');
    }
};
