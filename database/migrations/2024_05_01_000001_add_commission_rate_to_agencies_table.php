<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            // Add missing columns needed for the demo seeder
            if (!Schema::hasColumn('agencies', 'default_commission_rate')) {
                $table->decimal('default_commission_rate', 5, 2)->default(10.00);
            }
            
            if (!Schema::hasColumn('agencies', 'default_currency')) {
                $table->string('default_currency', 10)->default('SAR');
            }
            
            if (!Schema::hasColumn('agencies', 'price_decimals')) {
                $table->tinyInteger('price_decimals')->default(2);
            }
            
            if (!Schema::hasColumn('agencies', 'price_display_format')) {
                $table->string('price_display_format', 20)->default('symbol_first');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropColumn([
                'default_commission_rate',
                'default_currency',
                'price_decimals',
                'price_display_format'
            ]);
        });
    }
};
