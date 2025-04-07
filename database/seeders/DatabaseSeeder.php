<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // استدعاء البذور المختلفة
        $this->call([
            AgencySeeder::class,
            UserSeeder::class,
            ServiceSeeder::class,
            RequestSeeder::class,
            QuoteSeeder::class,
            CurrencySeeder::class,
        ]);
    }
}
