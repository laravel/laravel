<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Material;
use App\Models\Report;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard utama admin
     */
    public function index()
    {
        $stats = [
            'total_siswa'   => User::where('role', 'siswa')->count(),
            'total_guru'    => User::where('role', 'guru')->count(),
            'total_materi'  => Material::count(),
            'laporan_open'  => Report::where('status', 'open')->count(),
        ];

        $users = User::latest()->paginate(15);
        $recent_reports = Report::with('user')->latest()->limit(10)->get();

        return view('admin.dashboard', compact(
            'stats',
            'users',
            'recent_reports'
        ));
    }

    /**
     * Halaman laporan (admin)
     */
    public function reports()
    {
        $reports = Report::with('user')
            ->latest()
            ->paginate(15);

        return view('admin.reports', compact('reports'));
    }

    /**
     * Update status laporan (opsional tapi rapi)
     */
    public function updateReportStatus(Request $request, Report $report)
    {
        $request->validate([
            'status' => 'required|in:open,process,closed',
        ]);

        $report->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Status laporan diperbarui');
    }
}
