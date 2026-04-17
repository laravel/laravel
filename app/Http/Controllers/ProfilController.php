<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Golongan;
use App\Models\Jabatan;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $pegawai = $user->pegawai;
        
        if (!$pegawai) {
            return redirect()->route('dashboard')->with('error', 'Data pegawai tidak ditemukan.');
        }
        
        $pegawai->load(['golongan', 'jabatan', 'unitKerja']);
        
        return view('profil.index', compact('pegawai'));
    }

    public function edit()
    {
        $user = auth()->user();
        $pegawai = $user->pegawai;
        
        if (!$pegawai) {
            return redirect()->route('dashboard')->with('error', 'Data pegawai tidak ditemukan.');
        }
        
        $golongan = Golongan::orderBy('kode')->get();
        $jabatan = Jabatan::orderBy('nama')->get();
        $unitKerja = UnitKerja::orderBy('nama')->get();
        
        return view('profil.edit', compact('pegawai', 'golongan', 'jabatan', 'unitKerja'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $pegawai = $user->pegawai;
        
        if (!$pegawai) {
            return redirect()->route('dashboard')->with('error', 'Data pegawai tidak ditemukan.');
        }

        $request->validate([
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'status_perkawinan' => 'required|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati',
            'pendidikan_terakhir' => 'nullable|string|max:50',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only([
            'tempat_lahir', 'tanggal_lahir', 'alamat', 'telepon', 
            'email', 'status_perkawinan', 'pendidikan_terakhir'
        ]);
        
        if ($request->hasFile('foto')) {
            if ($pegawai->foto) {
                Storage::disk('public')->delete($pegawai->foto);
            }
            $data['foto'] = $request->file('foto')->store('foto-pegawai', 'public');
        }

        $pegawai->update($data);

        return redirect()->route('profil.index')->with('success', 'Profil berhasil diupdate.');
    }
}
