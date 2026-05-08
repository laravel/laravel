<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@elsda.com'],
            [
                'name'      => 'admin',
                'full_name' => 'Admin',
                'email'     => 'admin@elsda.com',
                'password'  => Hash::make('admin1234'),
                'phone'     => '09000000000',
                'address'   => 'elsda',
                'role'      => 'admin',
            ]
        );
    }
}