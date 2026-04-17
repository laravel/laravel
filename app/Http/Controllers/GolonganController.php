<?php

namespace App\Http\Controllers;

use App\Models\Golongan;
use Illuminate\Http\Request;

class GolonganController extends Controller
{
    public function index()
    {
        $golongan = Golongan::orderBy('kode')->paginate(10);
        return view('master.golongan.index', compact('golongan'));
    }

    public function create()
    {
        return view('master.golongan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:10|unique:golongan,kode',
            'nama' => 'required|string|max:100',
            'gaji_pokok' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        Golongan::create($request->all());

        return redirect()->route('golongan.index')->with('success', 'Data golongan berhasil ditambahkan.');
    }

    public function edit(Golongan $golongan)
    {
        return view('master.golongan.edit', compact('golongan'));
    }

    public function update(Request $request, Golongan $golongan)
    {
        $request->validate([
            'kode' => 'required|string|max:10|unique:golongan,kode,' . $golongan->id,
            'nama' => 'required|string|max:100',
            'gaji_pokok' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $golongan->update($request->all());

        return redirect()->route('golongan.index')->with('success', 'Data golongan berhasil diupdate.');
    }

    public function destroy(Golongan $golongan)
    {
        $golongan->delete();
        return redirect()->route('golongan.index')->with('success', 'Data golongan berhasil dihapus.');
    }
}
