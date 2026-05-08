<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('products', function (Blueprint $table) {
        if (!Schema::hasColumn('products', 'capital')) {
            $table->decimal('capital', 10, 2)->default(0)->after('stock');
        }
    });
}

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('capital');
        });
    }
};