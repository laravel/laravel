<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Show user's reports
     */
    public function index()
    {
        $reports = auth()->user()->reports()->latest()->get();
        return view('reports.index', compact('reports'));
    }

    /**
     * Show create report form
     */
    public function create()
    {
        return view('reports.create');
    }

    /**
     * Store new report
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10',
        ]);

        Report::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => 'open',
        ]);

        return redirect()->route('reports.index')->with('success', 'Laporan berhasil dibuat');
    }

    /**
     * Show report detail (admin only)
     */
    public function show(Report $report)
    {
        // Only admin or report creator can view
        if (auth()->user()->role !== 'admin' && auth()->user()->id !== $report->user_id) {
            abort(403);
        }

        return view('reports.show', compact('report'));
    }

    /**
     * Update report status (admin only)
     */
    public function update(Request $request, Report $report)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:open,process,solved',
            'solution' => 'nullable|string',
        ]);

        $report->update($validated);

        return back()->with('success', 'Laporan berhasil diupdate');
    }
}
