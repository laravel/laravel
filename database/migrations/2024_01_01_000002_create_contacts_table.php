<?php
// d:\Business\Homocerti\PHP version\backend\database\migrations\2024_01_01_000002_create_contacts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contacts', function (Blueprint $row) {
            $row->id();
            $row->string('reason')->nullable();
            $row->string('email');
            $row->string('name')->nullable();
            $row->text('message');
            $row->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contacts');
    }
};
