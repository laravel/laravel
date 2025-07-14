<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('test_attempts')->onDelete('cascade');
            $table->integer('score');
            $table->integer('total_questions');
            $table->float('percentage');
            $table->enum('status', ['passed', 'failed']);
            $table->string('duration');
            $table->json('answers');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('test_results');
    }
};
