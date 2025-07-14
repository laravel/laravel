<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Get category IDs
        $mathCategory = Category::where('name', 'Mathematics')->first();
        $logicalCategory = Category::where('name', 'Logical Reasoning')->first();
        $englishCategory = Category::where('name', 'English')->first();
        $progCategory = Category::where('name', 'Programming Fundamentals')->first();
        $webCategory = Category::where('name', 'Web Development')->first();
        $dbCategory = Category::where('name', 'Database')->first();
        $jsCategory = Category::where('name', 'JavaScript')->first();
        $phpCategory = Category::where('name', 'PHP')->first();
        $sqlCategory = Category::where('name', 'SQL')->first();

        $questions = [
            // Mathematics Questions
            [
                'test_id' => 1,
                'question' => 'What is the next number in the sequence: 2, 4, 8, 16, ___?',
                'options' => json_encode(['24', '32', '30', '28']),
                'correct_answer' => '32',
                'difficulty' => 'easy',
                'category_id' => $mathCategory->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'test_id' => 1,
                'question' => 'What is the value of π (pi) to two decimal places?',
                'options' => json_encode(['3.14', '3.16', '3.12', '3.18']),
                'correct_answer' => '3.14',
                'difficulty' => 'easy',
                'category_id' => $mathCategory->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Logical Reasoning Questions
            [
                'test_id' => 1,
                'question' => 'If all Bloops are Razzies and all Razzies are Lazzies, then all Bloops are definitely Lazzies.',
                'options' => json_encode(['True', 'False', 'Uncertain', 'None of the above']),
                'correct_answer' => 'True',
                'difficulty' => 'medium',
                'category_id' => $logicalCategory->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // English Questions
            [
                'test_id' => 1,
                'question' => 'Choose the correct sentence:',
                'options' => json_encode([
                    'She don\'t like apples',
                    'She doesn\'t likes apples',
                    'She doesn\'t like apples',
                    'She not like apples'
                ]),
                'correct_answer' => 'She doesn\'t like apples',
                'difficulty' => 'easy',
                'category_id' => $englishCategory->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Programming Fundamentals Questions
            [
                'test_id' => 2,
                'question' => 'What is the time complexity of accessing an element in an array by index?',
                'options' => json_encode(['O(1)', 'O(n)', 'O(log n)', 'O(n²)']),
                'correct_answer' => 'O(1)',
                'difficulty' => 'medium',
                'category_id' => $progCategory->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Web Development Questions
            [
                'test_id' => 2,
                'question' => 'Which of the following is NOT a CSS framework?',
                'options' => json_encode(['Bootstrap', 'Tailwind', 'React', 'Foundation']),
                'correct_answer' => 'React',
                'difficulty' => 'easy',
                'category_id' => $webCategory->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // JavaScript Questions
            [
                'test_id' => 2,
                'question' => 'What does the "===" operator do in JavaScript?',
                'options' => json_encode([
                    'Compares values for equality with type conversion',
                    'Assigns a value to a variable',
                    'Compares values for equality without type conversion',
                    'Checks if a variable is defined'
                ]),
                'correct_answer' => 'Compares values for equality without type conversion',
                'difficulty' => 'medium',
                'category_id' => $jsCategory->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // PHP Questions
            [
                'test_id' => 2,
                'question' => 'Which of the following is the correct way to start a PHP session?',
                'options' => json_encode([
                    'start_session()',
                    'session_begin()',
                    'session_start()',
                    'begin_session()'
                ]),
                'correct_answer' => 'session_start()',
                'difficulty' => 'easy',
                'category_id' => $phpCategory->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Database/SQL Questions
            [
                'test_id' => 2,
                'question' => 'Which SQL statement is used to update data in a database?',
                'options' => json_encode(['MODIFY', 'UPDATE', 'CHANGE', 'ALTER']),
                'correct_answer' => 'UPDATE',
                'difficulty' => 'easy',
                'category_id' => $sqlCategory->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Another Database Question
            [
                'test_id' => 2,
                'question' => 'Which SQL keyword is used to sort the result set?',
                'options' => json_encode(['ORDER BY', 'SORT BY', 'GROUP BY', 'ARRANGE BY']),
                'correct_answer' => 'ORDER BY',
                'difficulty' => 'easy',
                'category_id' => $sqlCategory->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($questions as $question) {
            Question::firstOrCreate(
                ['question' => $question['question']],
                $question
            );
        }
    }
}
