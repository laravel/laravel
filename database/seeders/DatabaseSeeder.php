<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agency;
use App\Models\User;
use App\Models\Service;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // إنشاء وكالة افتراضية
        $agency = Agency::create([
            'name' => 'وكالة السفر الافتراضية',
            'email' => 'agency@example.com',
            'phone' => '777123456',
            'address' => 'صنعاء، اليمن',
            'is_active' => true,
        ]);

        // إنشاء مستخدم وكيل
        $agencyUser = User::create([
            'name' => 'مدير الوكالة',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'phone' => '777654321',
            'user_type' => 'agency',
            'agency_id' => $agency->id,
            'is_active' => true,
        ]);

        // إنشاء سبوكيل
        $subagent = User::create([
            'name' => 'سبوكيل نموذجي',
            'email' => 'subagent@example.com',
            'password' => Hash::make('password'),
            'phone' => '777123123',
            'user_type' => 'subagent',
            'agency_id' => $agency->id,
            'parent_id' => $agencyUser->id,
            'is_active' => true,
        ]);

        // إنشاء عميل
        $customer = User::create([
            'name' => 'عميل نموذجي',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'phone' => '777456456',
            'user_type' => 'customer',
            'agency_id' => $agency->id,
            'parent_id' => $agencyUser->id,
            'is_active' => true,
        ]);

        // إنشاء خدمات نموذجية
        $services = [
            [
                'name' => 'موافقة أمنية - مصر',
                'description' => 'خدمة الموافقات الأمنية لدخول مصر',
                'type' => 'security_approval',
                'base_price' => 500,
                'commission_rate' => 10,
            ],
            [
                'name' => 'نقل بري - VIP',
                'description' => 'خدمة نقل بري VIP داخل اليمن وخارجها',
                'type' => 'transportation',
                'base_price' => 1000,
                'commission_rate' => 15,
            ],
            [
                'name' => 'حجز تذاكر طيران',
                'description' => 'خدمة حجز تذاكر الطيران لجميع الوجهات',
                'type' => 'flight',
                'base_price' => 2000,
                'commission_rate' => 5,
            ],
        ];

        foreach ($services as $serviceData) {
            $service = Service::create([
                'agency_id' => $agency->id,
                'name' => $serviceData['name'],
                'description' => $serviceData['description'],
                'type' => $serviceData['type'],
                'base_price' => $serviceData['base_price'],
                'commission_rate' => $serviceData['commission_rate'],
                'status' => 'active',
            ]);

            // ربط الخدمة بالسبوكيل
            if ($service->type != 'hajj_umrah') {
                $service->subagents()->attach($subagent->id, [
                    'is_active' => true,
                    'custom_commission_rate' => $serviceData['commission_rate'],
                ]);
            }
        }
    }
}
