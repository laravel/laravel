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
        Schema::create('system_state', function (Blueprint $table) {
            $table->id();
            $table->boolean('disaster_mode')->default(false);
            $table->boolean('check_occupancy')->default(false);
            $table->foreignId('current_disaster_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_state');
    }
};
