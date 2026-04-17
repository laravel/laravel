<?php

namespace App\Http\Controllers;

use App\Models\JenisCuti;
use Illuminate\Http\Request;

class JenisCutiController extends Controller
{
    public function index()
    {
        $jenisCuti = JenisCuti::orderBy('nama')->paginate(10);
        return view('master.jenis-cuti.index', compact('jenisCuti'));
    }

    public function create()
    {
        return view('master.jenis-cuti.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'max_hari' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        JenisCuti::create($request->all());

        return redirect()->route('jenis-cuti.index')->with('success', 'Data jenis cuti berhasil ditambahkan.');
    }

    public function edit(JenisCuti $jenis_cuti)
    {
        return view('master.jenis-cuti.edit', ['jenisCuti' => $jenis_cuti]);
    }

    public function update(Request $request, JenisCuti $jenis_cuti)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'max_hari' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $jenis_cuti->update($request->all());

        return redirect()->route('jenis-cuti.index')->with('success', 'Data jenis cuti berhasil diupdate.');
    }

    public function destroy(JenisCuti $jenis_cuti)
    {
        $jenis_cuti->delete();
        return redirect()->route('jenis-cuti.index')->with('success', 'Data jenis cuti berhasil dihapus.');
    }
}
