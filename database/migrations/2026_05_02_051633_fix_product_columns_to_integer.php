<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'price')) {
                $table->unsignedInteger('price')->default(0)->change();
            }
            if (Schema::hasColumn('products', 'stock')) {
                $table->unsignedInteger('stock')->default(0)->change();
            }
            if (Schema::hasColumn('products', 'capital')) {
                $table->unsignedInteger('capital')->default(0)->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0)->change();
            $table->decimal('stock', 10, 2)->default(0)->change();
            $table->decimal('capital', 10, 2)->default(0)->change();
        });
    }
};