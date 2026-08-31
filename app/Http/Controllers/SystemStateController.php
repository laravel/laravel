<?php

namespace App\Http\Controllers;

use App\Models\SystemState;
use Illuminate\Http\Request;

class SystemStateController extends Controller
{
    public function index()
    {
        $state = SystemState::with('currentDisaster')->first();
        return response()->json($state);
    }

    public function update(Request $request)
    {
        $request->validate([
            'disaster_mode' => 'boolean',
            'check_occupancy' => 'boolean',
            'current_disaster_id' => 'nullable|exists:disaster_events,id',
        ]);

        $state = SystemState::first();
        if (!$state) {
            $state = SystemState::create($request->all());
        } else {
            $state->update($request->all());
        }

        return response()->json($state->load('currentDisaster'));
    }
}
