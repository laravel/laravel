<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // supplier_codes: per-supplier product codes
            // Example: {"digiflazz": "mlbb-diamonds-86", "vipreseller": "ML86D"}
            // If a supplier key is missing, falls back to supplier_code field
            $table->json('supplier_codes')->nullable()->after('supplier_code');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('supplier_codes');
        });
    }
};
