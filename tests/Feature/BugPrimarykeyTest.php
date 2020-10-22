<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BugPrimaryKeyTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        //Create new test table with primary key named primary_key_custom_name but will be named test_pkey because dont work
        Schema::create('test', function (Blueprint $table) {
            $table->integer('number');
            $table->primary('number', 'primary_key_custom_name');
        });

        //Rename table
        Schema::rename("test", "test_renamed");

        //Delete primary_key_custom_name but will not find because was named test_pkey
        //But is this try to remove test_renamed_pkey and will fail
        Schema::table('test_renamed', function (Blueprint $table) {
            //This throw the error
            $table->dropPrimary('primary_key_custom_name');
        });
    }
}
