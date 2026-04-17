<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update existing "Honorer" to "Non ASN"
        DB::table('pegawai')
            ->where('status_pegawai', 'Honorer')
            ->update(['status_pegawai' => 'Non ASN']);

        // Modify the enum column to add new values
        DB::statement("ALTER TABLE pegawai MODIFY COLUMN status_pegawai ENUM('CPNS', 'PNS', 'PPPK', 'PPPK Paruh Waktu', 'Non ASN') DEFAULT 'PNS'");
    }

    public function down(): void
    {
        // Revert "Non ASN" back to "Honorer"
        DB::table('pegawai')
            ->where('status_pegawai', 'Non ASN')
            ->update(['status_pegawai' => 'Honorer']);

        // Revert "PPPK Paruh Waktu" to "PPPK"
        DB::table('pegawai')
            ->where('status_pegawai', 'PPPK Paruh Waktu')
            ->update(['status_pegawai' => 'PPPK']);

        // Revert enum
        DB::statement("ALTER TABLE pegawai MODIFY COLUMN status_pegawai ENUM('CPNS', 'PNS', 'PPPK', 'Honorer') DEFAULT 'PNS'");
    }
};
