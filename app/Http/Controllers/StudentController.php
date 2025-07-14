<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
class StudentController extends Controller
{

    public function index(Request $request)
    {
        $students = Student::with(['department', 'institute'])
            ->searchBy($request->search)
            ->paginate(5);
        return response()->json(['data' => $students]);
    }

    public function allStudents()
    {
        $students = Student::get();
        return response()->json(['data' => $students]);
    }

    public function show($id)
    {
        $student = Student::findOrFail($id);
        return response()->json(['data' => $student]);
    }
    public function store(Request $request)
    {
        // 1. Validate input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:students,phone',
            'institute_id' => 'required|exists:institutions,id',
            'department_id' => 'required|exists:departments,id',
            'email' => 'required|email|unique:students,email',
        ]);

        // 2. Create student with the validated data
        $student = Student::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'institute_id' => $validated['institute_id'],
            'department_id' => $validated['department_id'],
            'registration_date' => now(),
            'last_login' => now()
        ]);
        
        // 3. Load the relationships for the response
        $student->load('institute', 'department');
        
        // 4. Return created student with related data
        return response()->json([
            'message' => 'Student created successfully',
            'data' => $student
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $student->update($request->only(['name', 'phone', 'institute_id', 'department_id', 'email']));   
        $student->update([
            'last_login' => now()
        ]);
        return response()->json(['data' => $student]);
    }
    
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();
        return response()->json(['message' => 'Student deleted successfully.'], 200);
    }
}