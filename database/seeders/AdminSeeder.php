<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::updateOrCreate(
            ['email' => 'admin@cnadv.com.br'],
            [
                'name' => 'Administrador',
                'email' => 'admin@cnadv.com.br',
                'password' => Hash::make('admin'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create a test editor user
        User::updateOrCreate(
            ['email' => 'editor@cnadv.com.br'],
            [
                'name' => 'Editor',
                'email' => 'editor@cnadv.com.br',
                'password' => Hash::make('editor'),
                'role' => 'editor',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin and Editor users created successfully!');
        $this->command->info('Admin: admin@cnadv.com.br / admin');
        $this->command->info('Editor: editor@cnadv.com.br / editor');
    }
}
