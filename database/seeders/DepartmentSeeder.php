<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Computer Science', 'code' => 'CSE'],
            ['name' => 'Electrical Engineering', 'code' => 'EE'],
            ['name' => 'Mechanical Engineering', 'code' => 'ME'],
            ['name' => 'Civil Engineering', 'code' => 'CE'],
            ['name' => 'Chemical Engineering', 'code' => 'CHE'],
        ];

        foreach ($departments as $department) {
            DB::table('departments')->insert([
                'name' => $department['name'],
                'code' => $department['code'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
