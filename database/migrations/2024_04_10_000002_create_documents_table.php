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
        // تحقق من عدم وجود الجدول قبل محاولة إنشائه
        if (!Schema::hasTable('documents')) {
            Schema::create('documents', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('file_path');
                $table->string('file_type')->nullable();
                $table->unsignedBigInteger('size')->nullable();
                $table->morphs('documentable');
                $table->foreignId('uploaded_by')->constrained('users');
                $table->enum('visibility', ['public', 'private', 'agency'])->default('public');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // لا نقوم بحذف الجدول في هذه الحالة لأن هناك ملف هجرة آخر مسؤول عن ذلك
        Schema::dropIfExists('documents');
    }
    
};
