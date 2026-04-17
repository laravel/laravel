<?php

namespace App\Http\Controllers;

use App\Models\UpdateSchedule;
use Illuminate\Http\Request;

class UpdateScheduleController extends Controller
{
    public function edit()
    {
        $schedule = UpdateSchedule::current();

        return view('settings.update-schedule', compact('schedule'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'is_enabled' => 'nullable|boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'notes' => 'nullable|string|max:1000',
        ]);

        $schedule = UpdateSchedule::current() ?? new UpdateSchedule();
        $schedule->is_enabled = $request->boolean('is_enabled');
        $schedule->starts_at = $validated['starts_at'] ?? null;
        $schedule->ends_at = $validated['ends_at'] ?? null;
        $schedule->notes = $validated['notes'] ?? null;
        $schedule->updated_by = auth()->id();
        $schedule->save();

        return redirect()->route('settings.update-schedule.edit')
            ->with('success', 'Jadwal update data berhasil disimpan.');
    }
}
