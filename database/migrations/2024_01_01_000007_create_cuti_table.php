<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_cuti', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->integer('max_hari')->default(12);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('cuti', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat', 50)->unique();
            $table->foreignId('pegawai_id')->constrained('pegawai')->cascadeOnDelete();
            $table->foreignId('jenis_cuti_id')->constrained('jenis_cuti')->cascadeOnDelete();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('jumlah_hari');
            $table->text('alasan');
            $table->string('alamat_selama_cuti')->nullable();
            $table->string('telepon_darurat', 20)->nullable();
            $table->enum('status', ['Diajukan', 'Disetujui', 'Ditolak', 'Selesai'])->default('Diajukan');
            $table->text('keterangan')->nullable();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tanggal_disetujui')->nullable();
            $table->timestamps();
        });

        Schema::create('saldo_cuti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->cascadeOnDelete();
            $table->foreignId('jenis_cuti_id')->constrained('jenis_cuti')->cascadeOnDelete();
            $table->year('tahun');
            $table->integer('saldo_awal')->default(0);
            $table->integer('saldo_terpakai')->default(0);
            $table->integer('saldo_sisa')->default(0);
            $table->timestamps();
            
            $table->unique(['pegawai_id', 'jenis_cuti_id', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saldo_cuti');
        Schema::dropIfExists('cuti');
        Schema::dropIfExists('jenis_cuti');
    }
};
