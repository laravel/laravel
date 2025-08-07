<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        BlogPost::firstOrCreate(
            ['slug' => 'welcome-to-ai-saas'],
            [
                'title' => 'Welcome to the AI Agents SaaS',
                'excerpt' => 'Build and deploy AI chatbots in minutes.',
                'content' => 'This platform lets you create agents, embed them, and monetize with credits.',
                'status' => 'published',
            ]
        );
    }
}
