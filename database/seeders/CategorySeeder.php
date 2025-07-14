<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Core Categories
            ['name' => 'Mathematics', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Logical Reasoning', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'English', 'created_at' => now(), 'updated_at' => now()],
            
            // Programming Categories
            ['name' => 'Programming Fundamentals', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Web Development', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Database', 'created_at' => now(), 'updated_at' => now()],
            
            // Technology Specific
            ['name' => 'JavaScript', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PHP', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'SQL', 'created_at' => now(), 'updated_at' => now()],
        ];

        // Insert categories and get their IDs
        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
