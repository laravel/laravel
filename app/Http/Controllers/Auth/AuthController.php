<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function registerUser(Request $request)
    {

        $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6',
                'password_confirmation' => 'required|string|same:password'
            ],
            [
                'name.required' => 'Name field is required!',
                'email.required' => 'Email field is required',
                'email.unique' => 'This email is already taken.',
                'password.required' => 'Password is required',
                'password.min' => 'Password must be at least 6 characters',
                'password_confirmation.required' => 'Password confirmation is required',
                'password_confirmation.same' => 'Password confirmation must match password',
            ]
        );

        $user =  User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        if ($user) {

            return redirect()->route('login')->with('success', 'Registration done successfully');
        } else {
            return redirect()->back()->with('error', 'Unable to make registration');
        }
    }
}
