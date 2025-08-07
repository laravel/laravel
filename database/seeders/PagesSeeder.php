<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PagesSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            ['title' => 'Privacy Policy', 'slug' => 'privacy-policy', 'content' => 'Your privacy matters.'],
            ['title' => 'Terms of Service', 'slug' => 'terms-of-service', 'content' => 'Please read these terms carefully.'],
        ];
        foreach ($pages as $p) {
            Page::firstOrCreate(['slug' => $p['slug']], $p);
        }
    }
}
