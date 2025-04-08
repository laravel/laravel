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
            $agencyAdmin = User::create([
                'name' => 'مدير وكالة اليمن',
                'email' => 'admin@yemen-travel.com',
                'password' => Hash::make('password123'),
                'phone' => '777100100',
                'user_type' => 'agency',
                'agency_id' => $yemenAgency->id,
                'is_active' => true,
            ]);
            
            // إنشاء سبوكلاء لوكالة اليمن
            $subagent1 = User::create([
                'name' => 'أحمد محمد',
                'email' => 'ahmed@yemen-travel.com',
                'password' => Hash::make('password123'),
                'phone' => '777200200',
                'user_type' => 'subagent',
                'agency_id' => $yemenAgency->id,
                'parent_id' => $agencyAdmin->id,
                'is_active' => true,
            ]);
            
            $subagent2 = User::create([
                'name' => 'محمد علي',
                'email' => 'mohammed@yemen-travel.com',
                'password' => Hash::make('password123'),
                'phone' => '777300300',
                'user_type' => 'subagent',
                'agency_id' => $yemenAgency->id,
                'parent_id' => $agencyAdmin->id,
                'is_active' => true,
            ]);
            
            // إنشاء عملاء لوكالة اليمن
            User::create([
                'name' => 'سالم علي',
                'email' => 'salem@example.com',
                'password' => Hash::make('password123'),
                'phone' => '777400400',
                'user_type' => 'customer',
                'agency_id' => $yemenAgency->id,
                'parent_id' => $agencyAdmin->id,
                'is_active' => true,
            ]);
            
            User::create([
                'name' => 'فاطمة أحمد',
                'email' => 'fatima@example.com',
                'password' => Hash::make('password123'),
                'phone' => '777500500',
                'user_type' => 'customer',
                'agency_id' => $yemenAgency->id,
                'parent_id' => $agencyAdmin->id,
                'is_active' => true,
            ]);
        }
        
        if ($gulfAgency) {
            // إنشاء مستخدم وكيل لوكالة الخليج
            $gulfAdmin = User::create([
                'name' => 'مدير وكالة الخليج',
                'email' => 'admin@gulf-travel.com',
                'password' => Hash::make('password123'),
                'phone' => '777600600',
                'user_type' => 'agency',
                'agency_id' => $gulfAgency->id,
                'is_active' => true,
            ]);
            
            // إنشاء سبوكيل لوكالة الخليج
            User::create([
                'name' => 'خالد حسن',
                'email' => 'khaled@gulf-travel.com',
                'password' => Hash::make('password123'),
                'phone' => '777700700',
                'user_type' => 'subagent',
                'agency_id' => $gulfAgency->id,
                'parent_id' => $gulfAdmin->id,
                'is_active' => true,
            ]);
            
            // إنشاء عميل لوكالة الخليج
            User::create([
                'name' => 'عبد الله محمد',
                'email' => 'abdullah@example.com',
                'password' => Hash::make('password123'),
                'phone' => '777800800',
                'user_type' => 'customer',
                'agency_id' => $gulfAgency->id,
                'parent_id' => $gulfAdmin->id,
                'is_active' => true,
            ]);
        }
        
        // إنشاء مستخدم متميز للاختبار السريع
        User::create([
            'name' => 'مستخدم اختباري',
            'email' => 'test@example.com',
            'password' => Hash::make('123456'),
            'phone' => '777999999',
            'user_type' => 'agency',
            'agency_id' => $yemenAgency ? $yemenAgency->id : 1,
            'is_active' => true,
        ]);
    }
}
