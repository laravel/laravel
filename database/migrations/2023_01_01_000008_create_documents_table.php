<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); // owner
            $table->foreignId('request_id')->nullable()->constrained();
            $table->string('name');
            $table->string('file_path');
            $table->string('file_type');
            $table->bigInteger('file_size');
            $table->enum('visibility', ['private', 'agency', 'customer'])->default('private');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
};
