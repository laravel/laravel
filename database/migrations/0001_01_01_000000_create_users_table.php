<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up()
    {
        // Users Table (Admins)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->string('profile_picture')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->index('email');
        });

        // Universities Table
        Schema::create('universities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('vision')->nullable();
            $table->text('mission')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('location')->nullable();
            $table->enum('type', ['government', 'private'])->default('private');
            $table->timestamps();
            $table->index('slug');
            $table->index('type');
        });

        // Colleges Table
        Schema::create('colleges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('tagline')->nullable();
            $table->json('facilities')->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamps();
            $table->index('university_id');
            $table->index('slug');
        });

        // Courses Table
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('college_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('duration')->nullable();
            $table->decimal('fees', 10, 2)->nullable();
            $table->text('eligibility')->nullable();
            $table->text('scope')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
            $table->index('university_id');
            $table->index('college_id');
            $table->index('slug');
        });

        // Students Table
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('set null');
            $table->string('photo_path')->nullable();
            $table->timestamps();
            $table->index('email');
            $table->index('course_id');
        });

        // Enquiries Table
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('message');
            $table->foreignId('university_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['pending', 'contacted', 'closed'])->default('pending');
            $table->timestamps();
            $table->index('email');
            $table->index('university_id');
            $table->index('status');
        });

        // CMS Table
        Schema::create('cms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['slider', 'page', 'section']);
            $table->text('content');
            $table->string('image_path')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();
            $table->index('type');
            $table->index('status');
        });

        // Settings Table
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->default('UMS');
            $table->string('site_email')->nullable();
            $table->string('site_phone')->nullable();
            $table->string('site_address')->nullable();
            $table->timestamps();
        });

        // Notifications Table
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            $table->index('is_read');
        });

        // Sessions Table (Added for Laravel Session Driver)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('cms');
        Schema::dropIfExists('enquiries');
        Schema::dropIfExists('students');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('colleges');
        Schema::dropIfExists('universities');
        Schema::dropIfExists('users');
    }
};
