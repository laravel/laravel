<?php

namespace App\Http\Controllers;

use App\Models\UnitKerja;
use Illuminate\Http\Request;

class UnitKerjaController extends Controller
{
    public function index()
    {
        $unitKerja = UnitKerja::orderBy('kode')->paginate(10);
        return view('master.unit-kerja.index', compact('unitKerja'));
    }

    public function create()
    {
        return view('master.unit-kerja.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:20|unique:unit_kerja,kode',
            'nama' => 'required|string|max:150',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
        ]);

        UnitKerja::create($request->all());

        return redirect()->route('unit-kerja.index')->with('success', 'Data unit kerja berhasil ditambahkan.');
    }

    public function edit(UnitKerja $unit_kerja)
    {
        return view('master.unit-kerja.edit', ['unitKerja' => $unit_kerja]);
    }

    public function update(Request $request, UnitKerja $unit_kerja)
    {
        $request->validate([
            'kode' => 'required|string|max:20|unique:unit_kerja,kode,' . $unit_kerja->id,
            'nama' => 'required|string|max:150',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
        ]);

        $unit_kerja->update($request->all());

        return redirect()->route('unit-kerja.index')->with('success', 'Data unit kerja berhasil diupdate.');
    }

    public function destroy(UnitKerja $unit_kerja)
    {
        $unit_kerja->delete();
        return redirect()->route('unit-kerja.index')->with('success', 'Data unit kerja berhasil dihapus.');
    }
}
