<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DevUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'dev@example.com'],
            [
                'name' => 'Dev User',
                'password' => Hash::make('secret123'),
            ]
        );
    }
}
