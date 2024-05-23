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
        Schema::create('abstract_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('theme');
            $table->string('file_path');
            $table->unsignedBigInteger('user_id');
            $table->string('abstract_upload_id')->unique();
            $table->string('name'); 
            $table->string('organization_name'); 
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abstract_uploads');
    }
};
