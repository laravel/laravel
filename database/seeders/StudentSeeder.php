<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StudentSeeder extends Seeder
{
    public function run()
    {
        $students = [
            [
                'name' => 'Alice Johnson',
                'phone' => '1234567890',
                'email' => 'alice@example.com',
                'institute' => 1,
                'department' => 1,
                'registration_date' => now(),
                'last_login' => now(),
                'test_status' => 'active',
            ],
            [
                'name' => 'Bob Williams',
                'phone' => '2345678901',
                'email' => 'bob@example.com',
                'institute' => 2,
                'department' => 2,
                'registration_date' => now()->subDays(15),
                'last_login' => now()->subDays(2),
                'test_status' => 'active',
            ],
            [
                'name' => 'Charlie Brown',
                'phone' => '3456789012',
                'email' => 'charlie@example.com',
                'institute' => 3,
                'department' => 3,
                'registration_date' => now()->subDays(30),
                'last_login' => now()->subWeek(),
                'test_status' => 'inactive',
            ],
            [
                'name' => 'Diana Prince',
                'phone' => '4567890123',
                'email' => 'diana@example.com',
                'institute' => 4,
                'department' => 4,
                'registration_date' => now()->subDays(10),
                'last_login' => now()->subDay(),
                'test_status' => 'active',
            ],
            [
                'name' => 'Ethan Hunt',
                'phone' => '5678901234',
                'email' => 'ethan@example.com',
                'institute' => 5,
                'department' => 5,
                'registration_date' => now()->subDays(5),
                'last_login' => now(),
                'test_status' => 'active',
            ]
        ];

        foreach ($students as $student) {
            Student::create($student);
        }
    }
}
