<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rotarydies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('customermark');
            $table->decimal('aroundsize');
            $table->decimal('acrosssize');
            $table->integer('aroundrepeat');
            $table->integer('acrossrepeat');
            $table->decimal('aroundgap');
            $table->decimal('acrossgap');
            $table->decimal('cornerradius');
            $table->string('media');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rotarydies');
    }
};