<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $admins = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Content Manager',
                'email' => 'content@example.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Test Moderator',
                'email' => 'moderator@example.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Support Staff',
                'email' => 'support@example.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Reports Analyst',
                'email' => 'reports@example.com',
                'password' => Hash::make('password123'),
            ]
        ];

        foreach ($admins as $admin) {
            Admin::create($admin);
        }
    }
}
