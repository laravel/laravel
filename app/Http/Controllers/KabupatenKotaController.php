<?php

namespace App\Http\Controllers;

use App\Models\KabupatenKota;
use Illuminate\Http\Request;

class KabupatenKotaController extends Controller
{
    public function index()
    {
        $kabupatenKota = KabupatenKota::orderBy('nama')->paginate(10);
        return view('master.kabupaten-kota.index', compact('kabupatenKota'));
    }

    public function create()
    {
        return view('master.kabupaten-kota.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:10|unique:kabupaten_kota,kode',
            'nama' => 'required|string|max:100',
            'tipe' => 'required|in:Kabupaten,Kota',
            'provinsi' => 'nullable|string|max:100',
        ]);

        KabupatenKota::create($request->all());

        return redirect()->route('kabupaten-kota.index')->with('success', 'Data kabupaten/kota berhasil ditambahkan.');
    }

    public function edit(KabupatenKota $kabupatenKota)
    {
        return view('master.kabupaten-kota.edit', compact('kabupatenKota'));
    }

    public function update(Request $request, KabupatenKota $kabupatenKota)
    {
        $request->validate([
            'kode' => 'required|string|max:10|unique:kabupaten_kota,kode,' . $kabupatenKota->id,
            'nama' => 'required|string|max:100',
            'tipe' => 'required|in:Kabupaten,Kota',
            'provinsi' => 'nullable|string|max:100',
        ]);

        $kabupatenKota->update($request->all());

        return redirect()->route('kabupaten-kota.index')->with('success', 'Data kabupaten/kota berhasil diupdate.');
    }

    public function destroy(KabupatenKota $kabupatenKota)
    {
        $kabupatenKota->delete();
        return redirect()->route('kabupaten-kota.index')->with('success', 'Data kabupaten/kota berhasil dihapus.');
    }
}
