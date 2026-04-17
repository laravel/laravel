<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop columns yang tidak diperlukan
            $table->dropColumn(['name', 'email', 'email_verified_at']);
            
            // Add new columns
            $table->string('username', 50)->unique()->after('id');
            $table->string('nama', 100)->after('username');
            $table->enum('role', ['admin', 'pegawai'])->default('pegawai')->after('password');
            $table->foreignId('pegawai_id')->nullable()->after('role')->constrained('pegawai')->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('pegawai_id');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['pegawai_id']);
            $table->dropColumn(['username', 'nama', 'role', 'pegawai_id', 'is_active', 'last_login_at']);
            
            $table->string('name')->after('id');
            $table->string('email')->unique()->after('name');
            $table->timestamp('email_verified_at')->nullable()->after('email');
        });
    }
};
