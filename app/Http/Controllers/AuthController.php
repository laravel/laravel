<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Student;

class AuthController extends Controller
{
    // Student Login/Registration
    public function studentLogin(Request $request)
    {
        // 1. Validate incoming data
        $validated = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'institute_id' => 'required|string',
            'department_id' => 'required|string'
        ]);
        
        // 2. Check if student exists by phone
        $student = Student::where('phone', $validated['phone'])->first();
        if (!$student) {
            // 3a. CREATE new student if doesn't exist
            $student = Student::create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'institute_id' => $validated['institute_id'],
                'department_id' => $validated['department_id'],
                'registration_date' => now(),
                'test_status' => 'active',
                'last_login' => now()
            ]);
        } else {
            // 3b. UPDATE existing student info if exists
            $student->update([
                'name' => $validated['name'],
                'institute_id' => $validated['institute_id'],
                'department_id' => $validated['department_id'],
                'last_login' => now()
            ]);
        }
        
        // 4. Generate auth token
        $token = $student->createToken('student-token')->plainTextToken;
        
        // 5. Return student data + token
        return response()->json([
            'token' => $token,
            'user' => $student,
            'expires_at' => now()->addHours(24)
        ]);
    }
    
    // Admin Login
    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);
        if (!Auth::guard('admin')->attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        $admin = Auth::guard('admin')->user();
        $token = $admin->createToken('admin_token')->plainTextToken;
        return response()->json([
            'token' => $token,
            'user' => $admin,
            'expires_at' => now()->addHours(8)
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $user = auth('sanctum')->user();
        $user->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.']);
    }

    // Profile
    public function profile(Request $request)
    {
        $user = $request->user();
        if ($user instanceof Student) {
            $user->load('department', 'institute');
        }
        return response()->json($user);
    }
}

