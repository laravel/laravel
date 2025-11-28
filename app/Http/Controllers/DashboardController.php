<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // For now, no auth, just show dashboard
        return view('dashboard');
    }
}