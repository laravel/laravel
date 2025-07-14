<?php

namespace Database\Seeders;

use App\Models\TestAttempt;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TestAttemptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $attempts = [
            // Student 1's attempts
            [
                'test_id' => 1,
                'student_id' => 1,
                'started_at' => now()->subDays(5),
                'submitted_at' => now()->subDays(5)->addMinutes(45),
                'status' => 'completed',
            ],
            [
                'test_id' => 2,
                'student_id' => 1,
                'started_at' => now()->subDays(3),
                'submitted_at' => now()->subDays(3)->addMinutes(75),
                'status' => 'completed',
            ],
            
            // Student 2's attempts
            [
                'test_id' => 1,
                'student_id' => 2,
                'started_at' => now()->subWeek(),
                'submitted_at' => now()->subWeek()->addMinutes(50),
                'status' => 'completed',
            ],
            [
                'test_id' => 3,
                'student_id' => 2,
                'started_at' => now()->subDays(2),
                'submitted_at' => now()->subDays(2)->addMinutes(35),
                'status' => 'completed',
            ],
            
            // Student 3's attempts
            [
                'test_id' => 4,
                'student_id' => 3,
                'started_at' => now()->subDays(10),
                'submitted_at' => now()->subDays(10)->addMinutes(65),
                'status' => 'completed',
            ],
            
            // Student 4's attempts
            [
                'test_id' => 1,
                'student_id' => 4,
                'started_at' => now()->subDays(1),
                'submitted_at' => now()->subDays(1)->addMinutes(40),
                'status' => 'completed',
            ],
            [
                'test_id' => 5,
                'student_id' => 4,
                'started_at' => now()->subHours(5),
                'status' => 'in_progress',
            ],
            
            // Student 5's attempts
            [
                'test_id' => 2,
                'student_id' => 5,
                'started_at' => now()->subDays(2),
                'submitted_at' => now()->subDays(2)->addMinutes(55),
                'status' => 'completed',
            ]
        ];

        foreach ($attempts as $attempt) {
            TestAttempt::create($attempt);
        }
    }
}
