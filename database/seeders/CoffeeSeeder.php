<?php

namespace Database\Seeders;

use App\Models\Coffee;
use Illuminate\Database\Seeder;

class CoffeeSeeder extends Seeder
{
    public function run(): void
    {
        $coffees = [
            [
                'name' => 'Espresso',
                'description' => 'Strong and concentrated coffee shot',
                'price' => 3.50,
                'stock_quantity' => 100,
                'is_available' => true,
            ],
            [
                'name' => 'Cappuccino',
                'description' => 'Espresso with steamed milk and foam',
                'price' => 4.50,
                'stock_quantity' => 100,
                'is_available' => true,
            ],
            // Add more coffee types as needed
        ];

        foreach ($coffees as $coffee) {
            Coffee::create($coffee);
        }
    }
} 