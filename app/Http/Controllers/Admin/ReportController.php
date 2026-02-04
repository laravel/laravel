<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::latest()->paginate(20);
        return view('admin.reports.index', compact('reports'));
    }

    public function show(Report $report)
    {
        return view('admin.reports.show', compact('report'));
    }

    public function update(Request $request, Report $report)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,process,solved',
            'solution' => 'nullable|string',
        ]);

        $report->update($validated);

        return redirect()->route('admin.reports.index')->with('success', 'Laporan diperbarui');
    }
}
