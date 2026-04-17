<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update enum role untuk menambahkan sub_admin
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'sub_admin', 'pegawai') DEFAULT 'pegawai'");
        
        Schema::table('users', function (Blueprint $table) {
            // Add unit_kerja_id untuk sub_admin
            $table->foreignId('unit_kerja_id')->nullable()->after('pegawai_id')->constrained('unit_kerja')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['unit_kerja_id']);
            $table->dropColumn('unit_kerja_id');
        });
        
        // Revert enum role
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'pegawai') DEFAULT 'pegawai'");
    }
};
