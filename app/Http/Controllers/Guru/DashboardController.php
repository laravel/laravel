<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Material;

class DashboardController extends Controller
{
    public function index()
    {
        $materials = Material::where('guru_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('guru.dashboard', compact('materials'));
    }
}
