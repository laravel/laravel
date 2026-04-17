<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kgb', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_sk', 50)->unique();
            $table->foreignId('pegawai_id')->constrained('pegawai')->cascadeOnDelete();
            $table->date('tmt_kgb');
            $table->date('tmt_kgb_berikutnya');
            $table->foreignId('golongan_lama_id')->nullable()->constrained('golongan')->nullOnDelete();
            $table->foreignId('golongan_baru_id')->nullable()->constrained('golongan')->nullOnDelete();
            $table->decimal('gaji_pokok_lama', 15, 2)->default(0);
            $table->decimal('gaji_pokok_baru', 15, 2)->default(0);
            $table->integer('masa_kerja_tahun')->default(0);
            $table->integer('masa_kerja_bulan')->default(0);
            $table->enum('status', ['Diproses', 'Disetujui', 'Ditolak'])->default('Diproses');
            $table->text('keterangan')->nullable();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tanggal_disetujui')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kgb');
    }
};
