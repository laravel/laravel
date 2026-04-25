<?php
// d:\Business\Homocerti\PHP version\backend\database\migrations\2024_01_01_000001_create_bookings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $row) {
            $row->id();
            $row->string('service')->nullable();
            $row->date('date')->nullable();
            $row->string('email');
            $row->string('name')->nullable();
            $row->text('details')->nullable();
            $row->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};
