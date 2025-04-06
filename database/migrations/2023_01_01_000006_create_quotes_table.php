<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained();
            $table->foreignId('subagent_id')->constrained('users');
            $table->decimal('price', 10, 2);
            $table->decimal('commission_amount', 10, 2);
            $table->text('details')->nullable();
            $table->enum('status', ['pending', 'agency_approved', 'agency_rejected', 'customer_approved', 'customer_rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quotes');
    }
};
