<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    // Demo users
    private $users = [
        'admin' => [
            'username' => 'admin',
            'password' => 'admin123',
            'name' => 'Administrator',
            'role' => 'admin'
        ],
        'user' => [
            'username' => 'user', 
            'password' => 'user123',
            'name' => 'User Demo',
            'role' => 'user'
        ]
    ];

    public function showLoginForm()
    {
        // Jika sudah login, redirect ke choosedate
        if (Session::has('user')) {
            return redirect('/choosedate');
        }
        
        return view('login');
    }

    public function login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        // Validasi input
        if (empty($username) || empty($password)) {
            return redirect('/login')->with('error', 'Username dan password harus diisi!');
        }

        // Cek kredensial
        if (isset($this->users[$username]) && $this->users[$username]['password'] === $password) {
            // Login berhasil
            $user = $this->users[$username];
            
            // Simpan session
            Session::put('user', [
                'username' => $user['username'],
                'name' => $user['name'],
                'role' => $user['role'],
                'login_time' => now()
            ]);

            return redirect('/choosedate')->with('success', 'Login berhasil! Selamat datang ' . $user['name']);
        }

        // Login gagal
        return redirect('/login')->with('error', 'Username atau password salah!');
    }

    public function logout()
    {
        Session::forget('user');
        Session::flush();
        
        return redirect('/login')->with('success', 'Logout berhasil!');
    }
}
