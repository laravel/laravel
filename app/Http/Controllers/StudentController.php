<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        return response()->json(Student::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string|unique:students,student_id',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'rfid_tag' => 'required|string|unique:students,rfid_tag',
            'guardian_name' => 'required|string',
            'guardian_phone' => 'required|string',
        ]);

        $student = Student::create($request->all());

        return response()->json($student, 201);
    }

    public function show(Student $student)
    {
        return response()->json($student->load('occupancyLogs', 'rfidLogs'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'student_id' => 'string|unique:students,student_id,' . $student->id,
            'first_name' => 'string',
            'last_name' => 'string',
            'rfid_tag' => 'string|unique:students,rfid_tag,' . $student->id,
            'guardian_name' => 'string',
            'guardian_phone' => 'string',
        ]);

        $student->update($request->all());

        return response()->json($student);
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return response()->json(['message' => 'Student deleted successfully']);
    }
}
