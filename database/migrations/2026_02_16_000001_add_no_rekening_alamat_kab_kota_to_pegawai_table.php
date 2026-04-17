<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->string('no_rekening', 50)->nullable()->after('npwp');
            $table->string('alamat_kab_kota', 100)->nullable()->after('alamat');
        });
    }

    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropColumn(['no_rekening', 'alamat_kab_kota']);
        });
    }
};
