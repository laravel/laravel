<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create kabupaten_kota table
        Schema::create('kabupaten_kota', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique();
            $table->string('nama', 100);
            $table->enum('tipe', ['Kabupaten', 'Kota'])->default('Kabupaten');
            $table->string('provinsi', 100)->nullable();
            $table->timestamps();
        });

        // Add kabupaten_kota_id to pegawai table
        Schema::table('pegawai', function (Blueprint $table) {
            $table->foreignId('kabupaten_kota_id')->nullable()->after('alamat')->constrained('kabupaten_kota')->nullOnDelete();
        });

        // Remove old alamat_kab_kota column
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropColumn('alamat_kab_kota');
        });
    }

    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kabupaten_kota_id');
            $table->string('alamat_kab_kota', 100)->nullable()->after('alamat');
        });

        Schema::dropIfExists('kabupaten_kota');
    }
};
