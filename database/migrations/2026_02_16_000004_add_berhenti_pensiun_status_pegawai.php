<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Modify the enum column to add new values
        DB::statement("ALTER TABLE pegawai MODIFY COLUMN status_pegawai ENUM('CPNS', 'PNS', 'PPPK', 'PPPK Paruh Waktu', 'Non ASN', 'Berhenti/Keluar', 'Pensiun') DEFAULT 'PNS'");
    }

    public function down(): void
    {
        // Update records with new statuses to 'Non ASN' before reverting enum
        DB::table('pegawai')
            ->whereIn('status_pegawai', ['Berhenti/Keluar', 'Pensiun'])
            ->update(['status_pegawai' => 'Non ASN']);

        // Revert enum
        DB::statement("ALTER TABLE pegawai MODIFY COLUMN status_pegawai ENUM('CPNS', 'PNS', 'PPPK', 'PPPK Paruh Waktu', 'Non ASN') DEFAULT 'PNS'");
    }
};
