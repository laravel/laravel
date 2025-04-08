<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agency;

class AgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // وكالة افتراضية للتجربة
        Agency::create([
            'name' => 'وكالة اليمن للسفر والسياحة',
            'email' => 'info@yemen-travel.com',
            'phone' => '777123456',
            'address' => 'صنعاء - شارع جمال عبد الناصر',
            'is_active' => true,
        ]);

        Agency::create([
            'name' => 'وكالة الخليج للسفريات',
            'email' => 'info@gulf-travel.com',
            'phone' => '777654321',
            'address' => 'عدن - المنصورة',
            'is_active' => true,
        ]);

        // إضافة وكالات اختبارية إضافية (يمكن التعليق عليها في حالة عدم الرغبة)
        Agency::create([
            'name' => 'وكالة الشرق للسفر',
            'email' => 'info@east-travel.com',
            'phone' => '777111222',
            'address' => 'حضرموت - المكلا',
            'is_active' => true,
        ]);
    }
}
