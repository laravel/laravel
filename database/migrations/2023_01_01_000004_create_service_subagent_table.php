<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('service_subagent', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained();
            $table->foreignId('user_id')->constrained(); // subagent user_id
            $table->boolean('is_active')->default(true);
            $table->decimal('custom_commission_rate', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['service_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_subagent');
    }
};
