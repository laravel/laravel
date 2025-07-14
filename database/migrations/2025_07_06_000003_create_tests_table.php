<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('duration');
            $table->integer('total_questions');
            $table->enum('status', ['active', 'inactive', 'draft','scheduled','completed'])->default('active');
            $table->string('description');
            $table->unsignedInteger('passing_marks')->default(50);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('tests');
    }
};
