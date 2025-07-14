<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    /**
     * Get all institutions
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Institution::with(['department' => function($query) {
            $query->select('id', 'name', 'code');
        }])
        ->select('id', 'name', 'code', 'address', 'department_id')
        ->orderBy('name');

        // Filter by department_id if provided
        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $institutions = $query->paginate(5);

        return response()->json($institutions, 200);
    }

    public function all()
    {
        $institutions = Institution::with(['department' => function($query) {
            $query->select('id', 'name', 'code');
        }])
        ->select('id', 'name', 'code', 'address', 'department_id')
        ->orderBy('name')
        ->get();

        return response()->json($institutions, 200);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'address' => 'required|string',
            'department_id' => 'required|exists:departments,id',
        ]);

        $institution = Institution::create($validated);

        return response()->json($institution, 201);
    }
    public function show($id)
    {
        $institution = Institution::findOrFail($id);
        return response()->json($institution);
    }

    public function update(Request $request, $id)
    {
        $institution = Institution::findOrFail($id);
        $institution->update($request->only(['name', 'code', 'address', 'department_id']));
        return response()->json($institution);
    }

    public function destroy($id)
    {
        $institution = Institution::findOrFail($id);
        $institution->delete();
        return response()->json(['message' => 'Institution deleted.']);
    }   
}
