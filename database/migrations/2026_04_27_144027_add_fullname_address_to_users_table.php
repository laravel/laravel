<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add full_name after name
            if (!Schema::hasColumn('users', 'full_name')) {
                $table->string('full_name')->after('name')->nullable();
            }

            // Add phone if it doesn't exist yet
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->after('email')->nullable();
            }

            // Add address after phone
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->after('phone')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('users', 'full_name') ? 'full_name' : null,
                Schema::hasColumn('users', 'phone')     ? 'phone'     : null,
                Schema::hasColumn('users', 'address')   ? 'address'   : null,
            ]));
        });
    }
};