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
        Schema::create('disaster_events', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['fire', 'earthquake']);
            $table->enum('severity', ['critical', 'cautionary']);
            $table->foreignId('node_id');
            $table->string('location');
            $table->enum('status', ['active', 'resolved'])->default('active');
            $table->timestamp('started_at');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disaster_events');
    }
};
