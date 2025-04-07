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
        Schema::table('agencies', function (Blueprint $table) {
            if (!Schema::hasColumn('agencies', 'payment_settings')) {
                $table->json('payment_settings')->nullable();
            }
            if (!Schema::hasColumn('agencies', 'notification_settings')) {
                $table->json('notification_settings')->nullable();
            }
            if (!Schema::hasColumn('agencies', 'theme_color')) {
                $table->string('theme_color')->default('#007bff')->nullable();
            }
            if (!Schema::hasColumn('agencies', 'agency_language')) {
                $table->string('agency_language')->default('ar')->nullable();
            }
            if (!Schema::hasColumn('agencies', 'social_media_instagram')) {
                $table->string('social_media_instagram')->nullable();
            }
            if (!Schema::hasColumn('agencies', 'social_media_twitter')) {
                $table->string('social_media_twitter')->nullable();
            }
            if (!Schema::hasColumn('agencies', 'social_media_facebook')) {
                $table->string('social_media_facebook')->nullable();
            }
            if (!Schema::hasColumn('agencies', 'social_media_linkedin')) {
                $table->string('social_media_linkedin')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropColumn([
                'payment_settings',
                'notification_settings',
                'theme_color',
                'agency_language',
                'social_media_instagram',
                'social_media_twitter',
                'social_media_facebook',
                'social_media_linkedin',
            ]);
        });
    }
};
