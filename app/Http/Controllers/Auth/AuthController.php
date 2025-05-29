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
                'email' => 'required|email|unique',
                'password' => 'required|string|min:6',
                'password_confirmation' => 'required|string|same:password'
            ],
            [
                'name' => 'Name field is required!',
                'email' => 'Email field is required',
                'password' => 'must be at least 6 characters',
                'password_confirmation' => 'please the same the same passowrd'
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
