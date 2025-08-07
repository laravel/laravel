<?php

namespace Database\Seeders;

use App\Models\CreditPackage;
use Illuminate\Database\Seeder;

class CreditPackagesSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['name' => 'Starter', 'credits' => 500, 'price' => 5.00, 'currency' => 'USD', 'is_active' => true],
            ['name' => 'Pro', 'credits' => 2000, 'price' => 15.00, 'currency' => 'USD', 'is_active' => true],
            ['name' => 'Business', 'credits' => 10000, 'price' => 60.00, 'currency' => 'USD', 'is_active' => true],
        ];
        foreach ($defaults as $d) {
            CreditPackage::firstOrCreate(['name' => $d['name']], $d);
        }
    }
}
