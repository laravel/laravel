<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     public function up(): void
    {
        // Regions (Wilayah)
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Partners (Mitra per wilayah)
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });

        // Incoming Files (yang ada di folder /New)
        Schema::create('incoming_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->onDelete('cascade');
            $table->foreignId('region_id')->constrained()->onDelete('cascade'); // untuk query cepat
            $table->string('filename');
            $table->text('path')->nullable();
            $table->dateTime('detected_at');
            $table->timestamps();
        });

        // Archived Files (berasal dari folder /Processed)
        Schema::create('archived_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->onDelete('cascade');
            $table->foreignId('region_id')->constrained()->onDelete('cascade');
            $table->string('filename');
            $table->dateTime('moved_at');
            $table->timestamps();
        });

        // Notifications (notifikasi harian)
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained()->onDelete('cascade');
            $table->text('message');
            $table->dateTime('triggered_at');
            $table->timestamps();
        });

        // Scan Logs (opsional untuk log pemindaian)
        Schema::create('scan_logs', function (Blueprint $table) {
            $table->id();
            $table->dateTime('scan_time');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('result')->nullable(); // boleh json atau string
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scan_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('archived_files');
        Schema::dropIfExists('incoming_files');
        Schema::dropIfExists('partners');
        Schema::dropIfExists('regions');
    }
};
