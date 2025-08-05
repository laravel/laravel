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
        Schema::create('adminis', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role');
            $table->string('mobile');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('image')->nullable();
            $table->tinyInteger('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    
    public function down(): void
    {
        Schema::dropIfExists('adminis');
    }
};
