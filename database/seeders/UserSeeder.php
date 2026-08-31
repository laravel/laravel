<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@sensus.local'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'security@sensus.local'],
            [
                'name' => 'Security Personnel',
                'password' => Hash::make('password'),
                'role' => 'security',
            ]
        );

        User::firstOrCreate(
            ['email' => 'responder@sensus.local'],
            [
                'name' => 'Responder',
                'password' => Hash::make('password'),
                'role' => 'responder',
            ]
        );
    }
}
