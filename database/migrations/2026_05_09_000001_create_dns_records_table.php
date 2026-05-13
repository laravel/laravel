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
        Schema::create('dns_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // A, AAAA, CNAME, TXT, MX
            $table->string('name'); // e.g. @ or www
            $table->text('content');
            $table->boolean('proxied')->default(false);
            $table->integer('ttl')->default(1); // 1 = auto in Cloudflare
            $table->string('cloudflare_record_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dns_records');
    }
};
