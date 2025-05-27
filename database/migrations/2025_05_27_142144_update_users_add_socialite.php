<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();

            $table->string('socialite_provider')->nullable()->after('email');
            $table->string('socialite_id')->nullable()->after('socialite_provider');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable(false)->change();

            $table->dropColumn(['socialite_provider', 'socialite_id']);
        });
    }
};
