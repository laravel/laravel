<?php

namespace Database\Seeders;

use App\Models\TestResult;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $results = [
            // Results for Student 1
            [
                'attempt_id' => 1,
                'score' => 18,
                'total_questions' => 20,
                'percentage' => 90.0,
                'status' => 'passed',
                'duration' => '45m',
                'answers' => json_encode([
                    '1' => '32',
                    '2' => 'True'
                ]),
            ],
            [
                'attempt_id' => 2,
                'score' => 14,
                'total_questions' => 20,
                'percentage' => 70.0,
                'status' => 'passed',
                'duration' => '75m',
                'answers' => json_encode([
                    '3' => 'Compares values for equality without type conversion',
                    '4' => 'Stack'
                ]),
            ],
            
            // Results for Student 2
            [
                'attempt_id' => 3,
                'score' => 16,
                'total_questions' => 20,
                'percentage' => 80.0,
                'status' => 'passed',
                'duration' => '50m',
                'answers' => json_encode([
                    '1' => '32',
                    '2' => 'True'
                ]),
            ],
            [
                'attempt_id' => 4,
                'score' => 12,
                'total_questions' => 20,
                'percentage' => 60.0,
                'status' => 'passed',
                'duration' => '35m',
                'answers' => json_encode([
                    '5' => 'Cannot be determined'
                ]),
            ],
            
            // Results for Student 3
            [
                'attempt_id' => 5,
                'score' => 15,
                'total_questions' => 25,
                'percentage' => 60.0,
                'status' => 'passed',
                'duration' => '65m',
                'answers' => json_encode([
                    '6' => '3.14'
                ]),
            ],
            
            // Results for Student 4
            [
                'attempt_id' => 6,
                'score' => 17,
                'total_questions' => 20,
                'percentage' => 85.0,
                'status' => 'passed',
                'duration' => '40m',
                'answers' => json_encode([
                    '1' => '32',
                    '2' => 'True'
                ]),
            ],
            
            // Results for Student 5
            [
                'attempt_id' => 8,
                'score' => 13,
                'total_questions' => 20,
                'percentage' => 65.0,
                'status' => 'passed',
                'duration' => '55m',
                'answers' => json_encode([
                    '3' => 'Compares values for equality with type conversion',
                    '4' => 'Stack'
                ]),
            ]
        ];

        foreach ($results as $result) {
            TestResult::create($result);
        }
    }
}
