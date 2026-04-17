<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kebutuhan_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_kerja_id')->constrained('unit_kerja')->onDelete('cascade');
            $table->foreignId('jabatan_id')->constrained('jabatan')->onDelete('cascade');
            $table->integer('jumlah_kebutuhan')->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            // Unique constraint untuk kombinasi unit_kerja dan jabatan
            $table->unique(['unit_kerja_id', 'jabatan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kebutuhan_pegawai');
    }
};
