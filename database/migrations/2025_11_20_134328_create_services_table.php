<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g., "Business Hosting - domain.com"
            $table->string('type'); // hosting, domain, design, seo
            $table->string('status')->default('pending'); // pending, active, suspended, cancelled
            $table->decimal('price', 10, 2);
            $table->date('renewal_date')->nullable();
            $table->text('notes')->nullable(); // Admin notes
            $table->json('details')->nullable(); // Technical details (IP, credentials, etc.)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
