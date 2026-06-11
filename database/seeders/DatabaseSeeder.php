<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Restaurant;
use App\Models\MenuItem;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create categories
        $categories = [
            ['name' => 'Pizza', 'slug' => 'pizza', 'icon' => '🍕'],
            ['name' => 'Burgers', 'slug' => 'burgers', 'icon' => '🍔'],
            ['name' => 'Asian', 'slug' => 'asian', 'icon' => '🍜'],
            ['name' => 'Healthy', 'slug' => 'healthy', 'icon' => '🥗'],
            ['name' => 'Desserts', 'slug' => 'desserts', 'icon' => '🍰'],
        ];

        foreach ($categories as $index => $category) {
            Category::create(array_merge($category, ['order' => $index]));
        }

        // Create restaurants
        $restaurant1 = Restaurant::create([
            'name' => 'Pizza Paradise',
            'description' => 'Best pizza in town',
            'address' => '123 Main St',
            'phone' => '555-0100',
            'delivery_fee' => 3.99,
            'delivery_time' => 30,
            'minimum_order' => 15.00,
            'rating' => 4.5,
        ]);

        $restaurant2 = Restaurant::create([
            'name' => 'Burger House',
            'description' => 'Gourmet burgers',
            'address' => '456 Oak Ave',
            'phone' => '555-0200',
            'delivery_fee' => 2.99,
            'delivery_time' => 25,
            'minimum_order' => 10.00,
            'rating' => 4.7,
        ]);

        // Create menu items
        MenuItem::create([
            'restaurant_id' => $restaurant1->id,
            'category_id' => 1,
            'name' => 'Margherita Pizza',
            'description' => 'Classic Italian pizza',
            'price' => 12.99,
            'is_featured' => true,
        ]);

        MenuItem::create([
            'restaurant_id' => $restaurant2->id,
            'category_id' => 2,
            'name' => 'Classic Burger',
            'description' => 'Juicy beef patty',
            'price' => 9.99,
            'is_featured' => true,
        ]);
    }
}
