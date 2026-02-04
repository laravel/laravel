<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_siswa' => User::where('role', 'siswa')->count(),
            'total_guru' => User::where('role', 'guru')->count(),
            'total_materi' => 0,
            'laporan_open' => Report::where('status', 'open')->count(),
        ];
        $users = User::where('role', '!=', 'admin')->take(5)->get();
        $recent_reports = Report::with('user')->latest()->take(5)->get();
        return view('admin.dashboard', compact('stats', 'users', 'recent_reports'));
    }

    public function reports()
    {
        $recent_reports = Report::with('user')->latest()->get();
        return view('admin.reports', compact('recent_reports'));
    }
}
