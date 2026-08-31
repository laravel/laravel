<?php

namespace Database\Seeders;

use App\Models\Zone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    public function run(): void
    {
        Zone::create([
            'name' => 'Main Building',
            'description' => 'Primary academic building',
            'boundary_data' => null,
        ]);

        Zone::create([
            'name' => 'Science Building',
            'description' => 'Science laboratories and classrooms',
            'boundary_data' => null,
        ]);

        Zone::create([
            'name' => 'Library',
            'description' => 'Main library and study areas',
            'boundary_data' => null,
        ]);

        Zone::create([
            'name' => 'Cafeteria',
            'description' => 'Student dining area',
            'boundary_data' => null,
        ]);
    }
}
