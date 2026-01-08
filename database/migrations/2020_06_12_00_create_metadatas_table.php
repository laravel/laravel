<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetadatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('metadatas', function($table){
            $table->bigIncrements('id');
            $table->longtext('name');
            $table->longtext('comment')->nullable();
            $table->text('value');
            $table->boolean('is_visible')->default(true);
			
            $table->bigInteger('metadatatype')->unsigned()->nullable();
            $table->foreign('metadatatype')->references('id')->on('metadata_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('metadatas');
    }
}
