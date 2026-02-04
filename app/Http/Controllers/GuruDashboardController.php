<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class GuruDashboardController extends Controller
{
    public function index()
    {
        $materials = Material::where('user_id', auth()->id())->latest()->paginate(9);
        return view('guru.dashboard', compact('materials'));
    }

    public function chat()
    {
        return view('guru.chat');
    }

    public function reports()
    {
        return view('guru.reports');
    }
}
