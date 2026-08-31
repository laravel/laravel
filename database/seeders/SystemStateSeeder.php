<?php

namespace Database\Seeders;

use App\Models\SystemState;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemStateSeeder extends Seeder
{
    public function run(): void
    {
        SystemState::create([
            'disaster_mode' => false,
            'check_occupancy' => false,
            'current_disaster_id' => null,
        ]);
    }
}
