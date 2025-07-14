<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestAttempt;
use App\Models\Test;
use Illuminate\Support\Facades\DB;
use App\Models\TestResult;


class TestAttemptController extends Controller
{
    // Start a new test attempt
    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id'
        ]);
        $attempt = TestAttempt::create([
            'test_id' => $test_id,
            'student_id' => $data['student_id'],
            'started_at' => now(),
            'status' => 'in_progress'
        ]);
        return response()->json($attempt, 201);
    }

    // Get attempt details
    public function show($id)
    {
        $attempt = TestAttempt::findOrFail($id);
        return response()->json($attempt);
    }

    // Submit/finish an attempt
    public function update(Request $request, $id)
    {
        $attempt = TestAttempt::findOrFail($id);
        $attempt->update([
            'submitted_at' => now(),
            'status' => 'completed'
        ]);
        // Optionally, process and save answers here
        return response()->json($attempt);
    }
    
    // Start an attempt (client-side, just returns the attempt details)
    public function start(Request $request, $testId)
    {
        $student = auth('sanctum')->user();
        $existingAttempt = TestAttempt::where('test_id', $testId)
                                    ->where('student_id', $student->id)
                                    ->first();

        if ($existingAttempt) {
            return response()->json(['error' => 'Test already attempted'], 400);
        }

        $test = Test::findOrFail($testId);

        $attempt = TestAttempt::create([
            'test_id' => $testId,
            'student_id' => $student->id,
            'started_at' => now(),
            'status' => 'in_progress'
        ]);

        return response()->json([
            'data' => [
                'id' => $attempt->id,
                'test_id' => $testId,
                'started_at' => $attempt->started_at,
                'duration' => $test->duration
            ]
        ]);
    }

    // Submit an attempt (client-side, just returns the attempt details)
    public function submit(Request $request)
    {
        // 1. Validate the incoming request
        $validated = $request->validate([
            'attempt_id' => 'required|exists:test_attempts,id',
            'answers' => 'required|array'
        ]);
    
        // 2. Get the test attempt and verify ownership
        $attempt = TestAttempt::with('test.questions')->findOrFail($validated['attempt_id']);
        $student = auth('sanctum')->user();
    
        if ($attempt->student_id !== $student->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    
        // Prevent duplicate submissions
        if ($attempt->status === 'completed') {
            return response()->json(['error' => 'Test already submitted'], 400);
        }
    
        // 3. Calculate score and prepare answers data
        $questions = $attempt->test->questions;
        $totalQuestions = $questions->count();
        $correctAnswers = 0;
        $answersData = [];
    
        foreach ($validated['answers'] as $questionId => $selectedAnswer) {
            $question = $questions->find($questionId);
            if (!$question) continue;
    
            $isCorrect = $question->correct_answer === $selectedAnswer;
            if ($isCorrect) {
                $correctAnswers++;
            }
    
            $answersData[] = [
                'question_id' => $questionId,
                'selected_answer' => $selectedAnswer,
                'is_correct' => $isCorrect
            ];
        }
    
        // 4. Calculate results
        $percentage = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0;
        $status = $percentage >= 70 ? 'passed' : 'failed';
        
        // 5. Use database transaction to ensure data consistency
        DB::beginTransaction();
        try {
            // Update the attempt
            $attempt->update([
                'submitted_at' => now(),
                'status' => 'completed'
            ]);
    
            // Convert answers to JSON
            $answersJson = json_encode($answersData);
            
            // Create the test result
            $result = TestResult::create([
                'attempt_id' => $attempt->id,
                'score' => $correctAnswers,
                'total_questions' => $totalQuestions,
                'percentage' => $percentage,
                'status' => $status,
                'duration' => $attempt->started_at->diffInMinutes(now()),
                'answers' => $answersJson
            ]);
    
            DB::commit();
            return response()->json([
                'data' => $result,
                'message' => 'Test submitted successfully'
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to submit test',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the student's test history
     *
     * @return \Illuminate\Http\Response
     */
    public function studentTestHistory()
    {
        $student = auth('sanctum')->user();
        $tests = TestAttempt::where('student_id', $student->id)
                            ->with('test')
                            ->get();
        return response()->json(['data' => $tests]);
    }
}