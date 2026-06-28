<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // need_zone: true for ML (Server ID), Genshin (Server), HSR (Server)
            $table->boolean('need_zone')->default(false)->after('status');
            // zone_label: "Server ID", "Server", etc.
            $table->string('zone_label')->nullable()->default('Server ID')->after('need_zone');
            // target_label: "User ID", "UID", "Player ID", etc.
            $table->string('target_label')->nullable()->default('User ID')->after('zone_label');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['need_zone', 'zone_label', 'target_label']);
        });
    }
};
