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
            if (!Schema::hasColumn('agencies', 'notification_settings')) {
                $table->json('notification_settings')->nullable();
            }
            
            if (!Schema::hasColumn('agencies', 'email_settings')) {
                $table->json('email_settings')->nullable();
            }
            
            if (!Schema::hasColumn('agencies', 'commission_settings')) {
                $table->json('commission_settings')->nullable();
            }
            
            if (!Schema::hasColumn('agencies', 'price_decimals')) {
                $table->unsignedTinyInteger('price_decimals')->default(2);
            }
            
            if (!Schema::hasColumn('agencies', 'price_display_format')) {
                $table->string('price_display_format')->default('symbol_first');
            }
            
            if (!Schema::hasColumn('agencies', 'auto_convert_prices')) {
                $table->boolean('auto_convert_prices')->default(true);
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
                'notification_settings',
                'email_settings',
                'commission_settings',
                'price_decimals',
                'price_display_format',
                'auto_convert_prices',
            ]);
        });
    }
};
