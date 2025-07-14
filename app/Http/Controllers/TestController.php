<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\TestAttempt;

class TestController extends Controller
{
   
    // Get all active tests
    public function index(Request $request)
    {
        $tests = Test::with('questions')->paginate(5);
        return response()->json(['data' => $tests]);
    }
    
    public function allTests()
    {
        $tests = Test::with('questions')->get();
        return response()->json(['data' => $tests]);
    } 

    // Show a single test
    public function show($id)
    {
        $test = Test::findOrFail($id);
        return response()->json($test);
    }

    // Create a new test
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'duration' => 'required|integer',
            'total_questions' => 'required|integer',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
            'passing_marks' => 'nullable|integer',
        ]);
        $test = Test::create($data);
        return response()->json($test, 201);
    }

    // Update a test
    public function update(Request $request, $id)
    {
        $test = Test::findOrFail($id);
        $test->update($request->only(['title', 'duration', 'total_questions', 'status']));
        return response()->json($test);
    }

    // Delete a test
    public function destroy($id)
    {
        $test = Test::findOrFail($id);
        $test->delete();
        return response()->json(['message' => 'Test deleted.']);
    }
}
