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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['disaster', 'occupancy']);
            $table->string('recipient');
            $table->text('message');
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->timestamps();
            $table->timestamp('sent_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
