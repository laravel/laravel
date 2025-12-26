<?php

namespace App\Http\Controllers;

use App\Models\GuestEntry;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GuestEntryController extends Controller
{
    public function index()
    {
        return Inertia::render('Home', [
            'entries' => GuestEntry::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        GuestEntry::create($validated);

        return redirect()->back();
    }
}
