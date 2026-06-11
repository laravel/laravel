<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{

public function login(Request $request)
{
    // 1. Validate the form inputs
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    // 2. Attempt login
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        // 3. Dynamic redirection based on role
        if (Auth::user()->role === 'admin') {
            return redirect()->route('dashboard');
        } elseif (Auth::user()->role === 'customer') {
            return redirect()->route('home');
        }

       // return redirect()->route('dashboard');
    }

     // Return back if authentication fails
    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');

}

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Dynamic redirection based on role
        if (Auth::user()->role === 'admin') {
            return redirect()->route('dashboard');
        } elseif (Auth::user()->role === 'customer') {
            return redirect()->route('home');
        }

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
