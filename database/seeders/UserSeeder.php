<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Agency;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // جلب الوكالات المنشأة سابقاً
        $yemenAgency = Agency::where('email', 'info@yemen-travel.com')->first();
        $gulfAgency = Agency::where('email', 'info@gulf-travel.com')->first();
        
        if ($yemenAgency) {
            // إنشاء مستخدم وكيل لوكالة اليمن
            $agencyAdmin = User::firstOrCreate(
                ['email' => 'admin@yemen-travel.com'],
                [
                    'name' => 'مدير وكالة اليمن',
                    'password' => Hash::make('password123'),
                    'phone' => '777100100',
                    'user_type' => 'agency',
                    'agency_id' => $yemenAgency->id,
                    'is_active' => true,
                ]
            );
            
            // إنشاء سبوكلاء لوكالة اليمن
            $subagent1 = User::firstOrCreate(
                ['email' => 'ahmed@yemen-travel.com'],
                [
                    'name' => 'أحمد محمد',
                    'password' => Hash::make('password123'),
                    'phone' => '777200200',
                    'user_type' => 'subagent',
                    'agency_id' => $yemenAgency->id,
                    'parent_id' => $agencyAdmin->id,
                    'is_active' => true,
                ]
            );
            
            $subagent2 = User::firstOrCreate(
                ['email' => 'mohammed@yemen-travel.com'],
                [
                    'name' => 'محمد علي',
                    'password' => Hash::make('password123'),
                    'phone' => '777300300',
                    'user_type' => 'subagent',
                    'agency_id' => $yemenAgency->id,
                    'parent_id' => $agencyAdmin->id,
                    'is_active' => true,
                ]
            );
            
            // إنشاء عملاء لوكالة اليمن
            User::firstOrCreate(
                ['email' => 'salem@example.com'],
                [
                    'name' => 'سالم علي',
                    'password' => Hash::make('password123'),
                    'phone' => '777400400',
                    'user_type' => 'customer',
                    'agency_id' => $yemenAgency->id,
                    'parent_id' => $agencyAdmin->id,
                    'is_active' => true,
                ]
            );
            
            User::firstOrCreate(
                ['email' => 'fatima@example.com'],
                [
                    'name' => 'فاطمة أحمد',
                    'password' => Hash::make('password123'),
                    'phone' => '777500500',
                    'user_type' => 'customer',
                    'agency_id' => $yemenAgency->id,
                    'parent_id' => $agencyAdmin->id,
                    'is_active' => true,
                ]
            );
        }
        
        if ($gulfAgency) {
            // إنشاء مستخدم وكيل لوكالة الخليج
            $gulfAdmin = User::firstOrCreate(
                ['email' => 'admin@gulf-travel.com'],
                [
                    'name' => 'مدير وكالة الخليج',
                    'password' => Hash::make('password123'),
                    'phone' => '777600600',
                    'user_type' => 'agency',
                    'agency_id' => $gulfAgency->id,
                    'is_active' => true,
                ]
            );
            
            // إنشاء سبوكيل لوكالة الخليج
            User::firstOrCreate(
                ['email' => 'khaled@gulf-travel.com'],
                [
                    'name' => 'خالد حسن',
                    'password' => Hash::make('password123'),
                    'phone' => '777700700',
                    'user_type' => 'subagent',
                    'agency_id' => $gulfAgency->id,
                    'parent_id' => $gulfAdmin->id,
                    'is_active' => true,
                ]
            );
            
            // إنشاء عميل لوكالة الخليج
            User::firstOrCreate(
                ['email' => 'abdullah@example.com'],
                [
                    'name' => 'عبد الله محمد',
                    'password' => Hash::make('password123'),
                    'phone' => '777800800',
                    'user_type' => 'customer',
                    'agency_id' => $gulfAgency->id,
                    'parent_id' => $gulfAdmin->id,
                    'is_active' => true,
                ]
            );
        }
        
        // إنشاء مستخدم متميز للاختبار السريع
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'مستخدم اختباري',
                'password' => Hash::make('123456'),
                'phone' => '777999999',
                'user_type' => 'agency',
                'agency_id' => $yemenAgency ? $yemenAgency->id : 1,
                'is_active' => true,
            ]
        );
    }
}
