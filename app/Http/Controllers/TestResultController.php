<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestResult;

class TestResultController extends Controller
{
    // Get result for an attempt
    public function testResult($attemptId)
    {
        $result = TestResult::where('attempt_id', $attemptId)->firstOrFail();
        return response()->json($result);
    }
}