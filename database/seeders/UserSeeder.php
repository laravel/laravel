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
        // الحصول على معرفات الوكالات
        $yemenTravelId = Agency::where('email', 'info@yemen-travel.com')->first()->id ?? 1;
        $gulfTravelId = Agency::where('email', 'info@gulf-travel.com')->first()->id ?? 2;

        // إنشاء مديري الوكالات
        User::updateOrCreate(
            ['email' => 'admin@yemen-travel.com'],
            [
                'name' => 'مدير وكالة اليمن',
                'password' => Hash::make('password123'),
                'phone' => '777100100',
                'user_type' => 'agency',
                'agency_id' => $yemenTravelId,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@gulf-travel.com'],
            [
                'name' => 'مدير وكالة الخليج',
                'password' => Hash::make('password123'),
                'phone' => '777200200',
                'user_type' => 'agency',
                'agency_id' => $gulfTravelId,
                'is_active' => true,
            ]
        );

        // إنشاء السبوكلاء
        User::updateOrCreate(
            ['email' => 'ahmed@yemen-travel.com'],
            [
                'name' => 'أحمد محمد',
                'password' => Hash::make('password123'),
                'phone' => '777101201',
                'user_type' => 'subagent',
                'agency_id' => $yemenTravelId,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'mohammed@yemen-travel.com'],
            [
                'name' => 'محمد علي',
                'password' => Hash::make('password123'),
                'phone' => '777102202',
                'user_type' => 'subagent',
                'agency_id' => $yemenTravelId,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'khaled@gulf-travel.com'],
            [
                'name' => 'خالد حسن',
                'password' => Hash::make('password123'),
                'phone' => '777201301',
                'user_type' => 'subagent',
                'agency_id' => $gulfTravelId,
                'is_active' => true,
            ]
        );

        // إنشاء العملاء
        User::updateOrCreate(
            ['email' => 'salem@example.com'],
            [
                'name' => 'سالم علي',
                'password' => Hash::make('password123'),
                'phone' => '777301401',
                'user_type' => 'customer',
                'agency_id' => $yemenTravelId,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'fatima@example.com'],
            [
                'name' => 'فاطمة أحمد',
                'password' => Hash::make('password123'),
                'phone' => '777302402',
                'user_type' => 'customer',
                'agency_id' => $yemenTravelId,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'abdullah@example.com'],
            [
                'name' => 'عبد الله محمد',
                'password' => Hash::make('password123'),
                'phone' => '777303403',
                'user_type' => 'customer',
                'agency_id' => $gulfTravelId,
                'is_active' => true,
            ]
        );
    }
}
