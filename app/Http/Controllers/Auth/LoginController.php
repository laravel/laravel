<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{


    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validate the login request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Retrieve the user from the database by email
        $user = User::where('email', $request->email)->first();

        // Check if user exists and password matches
        if ($user && $user->password === $request->password) {
            // Authentication passed
            // Log the user in
            Auth::login($user);
            return redirect()->route('profile'); // Redirect to profile page after successful login
        }

        // Authentication failed
        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }
    
}

