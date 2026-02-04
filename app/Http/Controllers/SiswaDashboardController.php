<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SiswaDashboardController extends Controller
{
    public function index()
    {
        return view('siswa.dashboard');
    }

    public function chat()
    {
        return view('siswa.chat');
    }

    public function reports()
    {
        return view('siswa.reports');
    }
}
