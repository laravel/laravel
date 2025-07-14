<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TestAttempt;
use App\Models\TestResult;
use App\Models\Student;
use App\Models\Test;
use App\Models\Department;
use App\Models\Institution;

class AnalyticsController extends Controller
{
    // Get analytics with filters
    public function results(Request $request)
    {
        // 1. Base query for completed attempts
        $query = TestAttempt::with(['student', 'test'])
                        ->where('status', 'completed');

        // 2. Apply filters
        if ($request->has('status')) {
            $query->whereHas('testResult', function($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        if ($request->has('institute')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('institute', $request->institute);
            });
        }

        // 3. Get results with statistics
        $attempts = $query->get();

        // 4. Calculate summary statistics
        $summary = [
            'total_attempts' => $attempts->count(),
            'passed_students' => $attempts->where('percentage', '>=', 70)->count(),
            'failed_students' => $attempts->where('percentage', '<', 70)->count(),
            'average_score' => $attempts->avg('percentage')
        ];

        // 5. Format detailed results
        $results = $attempts->map(function($attempt) {
            return [
                'student_name' => $attempt->student->name,
                'institute' => $attempt->student->institute,
                'department' => $attempt->student->department,
                'score' => $attempt->score,
                'total_questions' => $attempt->test->total_questions,
                'percentage' => $attempt->percentage,
                'test_date' => $attempt->submitted_at,
                'duration' => $attempt->started_at->diffInMinutes($attempt->submitted_at),
                'status' => $attempt->percentage >= 70 ? 'passed' : 'failed'
            ];
        });

        return response()->json([
            'data' => [
                'summary' => $summary,
                'results' => $results
            ]
        ]);

    }
}