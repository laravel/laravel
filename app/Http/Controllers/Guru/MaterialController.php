<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $materials = auth()->user()->materials()->latest()->get();
        return view('guru.materials.index', compact('materials'));
    }

    public function create()
    {
        return view('guru.materials.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        $material = new Material($validated);
        $material->user_id = auth()->id();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('materials', $filename, 'public');
            $material->file_path = $path;
            $material->file_name = $file->getClientOriginalName();
        }

        $material->save();

        return redirect()->route('guru.materials.index')->with('success', 'Materi berhasil ditambahkan');
    }

    public function edit(Material $material)
    {
        $this->authorize('update', $material);
        return view('guru.materials.edit', compact('material'));
    }

    public function update(Request $request, Material $material)
    {
        $this->authorize('update', $material);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        $material->update($validated);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('materials', $filename, 'public');
            $material->file_path = $path;
            $material->file_name = $file->getClientOriginalName();
            $material->save();
        }

        return redirect()->route('guru.materials.index')->with('success', 'Materi berhasil diperbarui');
    }

    public function destroy(Material $material)
    {
        $this->authorize('delete', $material);
        $material->delete();
        return back()->with('success', 'Materi berhasil dihapus');
    }
}
