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
        // Use firstOrCreate to avoid duplicate entries
        Agency::firstOrCreate(
            ['email' => 'info@yemen-travel.com'],
            [
                'name' => 'وكالة اليمن للسفر والسياحة',
                'phone' => '777123456',
                'address' => 'صنعاء - شارع جمال عبد الناصر',
                'is_active' => true
            ]
        );

        Agency::firstOrCreate(
            ['email' => 'info@gulf-travel.com'],
            [
                'name' => 'وكالة الخليج للسفريات',
                'phone' => '777654321',
                'address' => 'عدن - المنصورة',
                'is_active' => true
            ]
        );

        Agency::firstOrCreate(
            ['email' => 'info@east-travel.com'],
            [
                'name' => 'وكالة الشرق للسفر',
                'phone' => '777111222',
                'address' => 'حضرموت - المكلا',
                'is_active' => true
            ]
        );
    }
}
