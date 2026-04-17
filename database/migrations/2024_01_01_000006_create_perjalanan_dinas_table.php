<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perjalanan_dinas', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat', 50)->unique();
            $table->foreignId('pegawai_id')->constrained('pegawai')->cascadeOnDelete();
            $table->date('tanggal_berangkat');
            $table->date('tanggal_kembali');
            $table->string('tujuan', 200);
            $table->text('maksud_perjalanan');
            $table->enum('jenis_transportasi', ['Darat', 'Laut', 'Udara'])->default('Darat');
            $table->decimal('biaya_transport', 15, 2)->default(0);
            $table->decimal('biaya_penginapan', 15, 2)->default(0);
            $table->decimal('uang_harian', 15, 2)->default(0);
            $table->decimal('biaya_lainnya', 15, 2)->default(0);
            $table->decimal('total_biaya', 15, 2)->default(0);
            $table->enum('status', ['Diajukan', 'Disetujui', 'Ditolak', 'Selesai'])->default('Diajukan');
            $table->text('keterangan')->nullable();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tanggal_disetujui')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perjalanan_dinas');
    }
};
