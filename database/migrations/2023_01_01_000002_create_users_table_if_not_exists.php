<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->string('password');
                $table->enum('user_type', ['agency', 'subagent', 'customer']);
                $table->foreignId('agency_id')->nullable()->constrained();
                $table->foreignId('parent_id')->nullable()->references('id')->on('users');
                $table->boolean('is_active')->default(true);
                $table->rememberToken();
                $table->timestamps();
            });
        } else {
            // الجدول موجود بالفعل، تحقق من إضافة الأعمدة الجديدة
            if (!Schema::hasColumn('users', 'phone')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('phone')->nullable()->after('email');
                });
            }
            
            if (!Schema::hasColumn('users', 'user_type')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->enum('user_type', ['agency', 'subagent', 'customer'])->default('customer')->after('password');
                });
            }
            
            if (!Schema::hasColumn('users', 'agency_id')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->foreignId('agency_id')->nullable()->after('user_type');
                });
            }
            
            if (!Schema::hasColumn('users', 'parent_id')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->foreignId('parent_id')->nullable()->after('agency_id');
                });
            }
            
            if (!Schema::hasColumn('users', 'is_active')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->boolean('is_active')->default(true)->after('parent_id');
                });
            }
        }
    }

    public function down()
    {
        // لا تحذف جدول المستخدمين في حالة التراجع
        // Schema::dropIfExists('users');
    }
};
