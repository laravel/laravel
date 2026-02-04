<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        $materials = Material::latest()->paginate(10);
        return view('siswa.dashboard', compact('materials'));
    }

    public function download(Material $material)
    {
        if (!$material->file_path) {
            return back()->with('error', 'File tidak tersedia');
        }

        return Storage::disk('public')->download($material->file_path, $material->file_name);
    }
}
