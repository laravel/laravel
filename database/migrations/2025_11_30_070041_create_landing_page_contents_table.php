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
        Schema::create('landing_page_contents', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Unique identifier for content (e.g., hero_title, services_heading)');
            $table->json('value')->comment('Multilingual content in JSON format: {"en":"English text","ar":"Arabic text"}');
            $table->string('section')->index()->comment('Content section grouping (hero, services, pricing, etc.)');
            $table->enum('type', ['text', 'textarea', 'rich_text'])->default('text')->comment('Content type for form rendering');
            $table->text('description')->nullable()->comment('Admin-facing description of what this content is for');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_contents');
    }
};
