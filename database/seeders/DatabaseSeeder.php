<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // ArcanePay Seed Data
        $games = [
            ['name' => 'Mobile Legends', 'slug' => 'mobile-legends', 'icon' => ''],
            ['name' => 'Free Fire', 'slug' => 'free-fire', 'icon' => ''],
            ['name' => 'PUBG Mobile', 'slug' => 'pubg-mobile', 'icon' => ''],
            ['name' => 'Genshin Impact', 'slug' => 'genshin-impact', 'icon' => ''],
            ['name' => 'Valorant', 'slug' => 'valorant', 'icon' => ''],
        ];

        foreach ($games as $game) {
            \App\Models\Category::create($game);
        }

        $products = [
            ['category_id' => 1, 'name' => '86 Diamonds', 'supplier_code' => 'ML86', 'base_price' => 10000, 'sell_price' => 12000],
            ['category_id' => 1, 'name' => '172 Diamonds', 'supplier_code' => 'ML172', 'base_price' => 19000, 'sell_price' => 22000],
            ['category_id' => 1, 'name' => '257 Diamonds', 'supplier_code' => 'ML257', 'base_price' => 28000, 'sell_price' => 32000],
            ['category_id' => 1, 'name' => '344 Diamonds', 'supplier_code' => 'ML344', 'base_price' => 37000, 'sell_price' => 42000],
            ['category_id' => 1, 'name' => '514 Diamonds', 'supplier_code' => 'ML514', 'base_price' => 55000, 'sell_price' => 62000],
            ['category_id' => 2, 'name' => '50 Diamonds', 'supplier_code' => 'FF50', 'base_price' => 7000, 'sell_price' => 9000],
            ['category_id' => 2, 'name' => '100 Diamonds', 'supplier_code' => 'FF100', 'base_price' => 13000, 'sell_price' => 16000],
            ['category_id' => 2, 'name' => '310 Diamonds', 'supplier_code' => 'FF310', 'base_price' => 38000, 'sell_price' => 44000],
            ['category_id' => 3, 'name' => '50 UC', 'supplier_code' => 'PUBG50', 'base_price' => 9000, 'sell_price' => 11000],
            ['category_id' => 3, 'name' => '100 UC', 'supplier_code' => 'PUBG100', 'base_price' => 17000, 'sell_price' => 20000],
        ];

        foreach ($products as $product) {
            \App\Models\Product::create($product);
        }
    }
}
