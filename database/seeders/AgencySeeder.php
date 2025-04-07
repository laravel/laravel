<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agency;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء وكالة اليمن للسفر
        $agency1 = Agency::firstOrCreate(
            ['email' => 'info@yemen-travel.com'],
            [
                'name' => 'وكالة اليمن للسفر والسياحة',
                'phone' => '777123456',
                'address' => 'صنعاء - شارع جمال عبد الناصر',
                'is_active' => true,
            ]
        );

        // إنشاء مدير الوكالة إذا لم يكن موجودًا
        if (!User::where('email', 'admin@yemen-travel.com')->exists()) {
            User::create([
                'name' => 'مدير وكالة اليمن',
                'email' => 'admin@yemen-travel.com',
                'password' => Hash::make('password123'),
                'user_type' => 'agency',
                'agency_id' => $agency1->id,
                'is_active' => true,
            ]);
        }

        // إنشاء وكالة الخليج للسفريات
        $agency2 = Agency::firstOrCreate(
            ['email' => 'info@gulf-travel.com'],
            [
                'name' => 'وكالة الخليج للسفريات',
                'phone' => '736789012',
                'address' => 'الرياض - حي العليا',
                'is_active' => true,
            ]
        );

        // إنشاء مدير الوكالة إذا لم يكن موجودًا
        if (!User::where('email', 'admin@gulf-travel.com')->exists()) {
            User::create([
                'name' => 'مدير وكالة الخليج',
                'email' => 'admin@gulf-travel.com',
                'password' => Hash::make('password123'),
                'user_type' => 'agency',
                'agency_id' => $agency2->id,
                'is_active' => true,
            ]);
        }

        // ... المزيد من الوكالات إذا لزم الأمر ...
    }
}
