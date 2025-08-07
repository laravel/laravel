<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CreditPackagesSeeder::class,
            PagesSeeder::class,
            BlogSeeder::class,
        ]);
    }
}
