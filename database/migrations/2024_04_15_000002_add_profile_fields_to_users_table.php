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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable();
            }
            
            if (!Schema::hasColumn('users', 'id_number')) {
                $table->string('id_number')->nullable();
            }
            
            if (!Schema::hasColumn('users', 'passport_number')) {
                $table->string('passport_number')->nullable();
            }
            
            if (!Schema::hasColumn('users', 'nationality')) {
                $table->string('nationality')->nullable();
            }
            
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable();
            }
            
            if (!Schema::hasColumn('users', 'country')) {
                $table->string('country')->nullable();
            }
            
            if (!Schema::hasColumn('users', 'preferred_currency')) {
                $table->string('preferred_currency')->nullable();
            }
            
            if (!Schema::hasColumn('users', 'notification_preferences')) {
                $table->json('notification_preferences')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar',
                'id_number',
                'passport_number',
                'nationality',
                'city',
                'country',
                'preferred_currency',
                'notification_preferences',
            ]);
        });
    }
};
