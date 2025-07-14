<?php

namespace Database\Seeders;

use App\Models\Test;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $tests = [
            [
                'title' => 'General Aptitude Test',
                'duration' => 60,
                'total_questions' => 25,
                'status' => 'active',
                'description' => 'Test your general knowledge and aptitude skills',
                'passing_marks' => 50
            ],
            [
                'title' => 'Technical Assessment - Programming',
                'duration' => 90,
                'total_questions' => 30,
                'status' => 'active',
                'description' => 'Test your programming knowledge and problem-solving skills',
                'passing_marks' => 60
            ],
            [
                'title' => 'Logical Reasoning Test',
                'duration' => 45,
                'total_questions' => 20,
                'status' => 'active',
                'description' => 'Evaluate your logical thinking and problem-solving abilities',
                'passing_marks' => 55
            ],
            [
                'title' => 'Mathematics Challenge',
                'duration' => 75,
                'total_questions' => 25,
                'status' => 'active',
                'description' => 'Test your mathematical skills and numerical ability',
                'passing_marks' => 50
            ],
            [
                'title' => 'Web Development Quiz',
                'duration' => 60,
                'total_questions' => 20,
                'status' => 'draft',
                'description' => 'Coming soon: Test your web development knowledge',
                'passing_marks' => 60
            ],
            [
                'title' => 'Database Concepts',
                'duration' => 50,
                'total_questions' => 25,
                'status' => 'scheduled',
                'description' => 'Test your understanding of database concepts and SQL',
                'passing_marks' => 55
            ],
            [
                'title' => 'Language Proficiency - English',
                'duration' => 40,
                'total_questions' => 30,
                'status' => 'active',
                'description' => 'Assess your English language proficiency',
                'passing_marks' => 50
            ]
        ];

        foreach ($tests as $test) {
            Test::create($test);
        }
    }
}
