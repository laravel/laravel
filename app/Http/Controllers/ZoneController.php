<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    public function index()
    {
        return response()->json(Zone::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'boundary_data' => 'nullable|array',
        ]);

        $zone = Zone::create($request->all());

        return response()->json($zone, 201);
    }

    public function show(Zone $zone)
    {
        return response()->json($zone->load('nodes'));
    }

    public function update(Request $request, Zone $zone)
    {
        $request->validate([
            'name' => 'string',
            'description' => 'nullable|string',
            'boundary_data' => 'nullable|array',
        ]);

        $zone->update($request->all());

        return response()->json($zone);
    }

    public function destroy(Zone $zone)
    {
        $zone->delete();

        return response()->json(['message' => 'Zone deleted successfully']);
    }
}
