<?php

namespace App\Http\Controllers;

use App\Helpers\IdEncoder;
use App\Models\Pegawai;
use App\Models\Golongan;
use App\Models\Jabatan;
use App\Models\UnitKerja;
use App\Models\KabupatenKota;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PegawaiController extends Controller
{
    private function decodeFilterId($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = (string) $value;
        $decoded = IdEncoder::decode($value);

        if ($decoded !== null) {
            return $decoded;
        }

        return is_numeric($value) ? (int) $value : null;
    }

    private function encodeFilterId($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? IdEncoder::encode((int) $value) : null;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Pegawai::with(['golongan', 'jabatan', 'unitKerja', 'kabupatenKota']);
        $selectedUnitKerjaId = $this->decodeFilterId($request->get('unit_kerja_id'));
        
        // Sub admin only sees pegawai from their unit kerja
        if ($user->isSubAdmin()) {
            $query->where('unit_kerja_id', $user->unit_kerja_id);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nip', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }
        
        // Admin can filter by unit_kerja, sub_admin filter is already applied
        if ($request->filled('unit_kerja_id') && $user->isAdmin()) {
            if ($selectedUnitKerjaId) {
                $query->where('unit_kerja_id', $selectedUnitKerjaId);
            }
        }
        
        $pegawai = $query->orderBy('nama')->paginate(10)->withQueryString();
        
        // Sub admin only sees their own unit kerja in filter
        if ($user->isSubAdmin()) {
            $unitKerja = UnitKerja::where('id', $user->unit_kerja_id)->get();
        } else {
            $unitKerja = UnitKerja::orderBy('nama')->get();
        }

        $selectedUnitKerjaParam = $this->encodeFilterId($selectedUnitKerjaId);
        
        return view('pegawai.index', compact('pegawai', 'unitKerja', 'selectedUnitKerjaParam'));
    }

    public function create()
    {
        $user = auth()->user();
        $golongan = Golongan::orderBy('kode')->get();
        $jabatan = Jabatan::orderBy('nama')->get();
        $kabupatenKota = KabupatenKota::orderBy('nama')->get();
        
        // Get distinct eselon values for dropdown
        $eselonList = Jabatan::whereNotNull('eselon')->where('eselon', '!=', '')->distinct()->orderBy('eselon')->pluck('eselon');
        
        // Sub admin only sees their own unit kerja
        if ($user->isSubAdmin()) {
            $unitKerja = UnitKerja::where('id', $user->unit_kerja_id)->get();
        } else {
            $unitKerja = UnitKerja::orderBy('nama')->get();
        }
        
        return view('pegawai.create', compact('golongan', 'jabatan', 'unitKerja', 'kabupatenKota', 'eselonList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|string|max:30|unique:pegawai,nip',
            'nik' => 'nullable|string|max:16|unique:pegawai,nik',
            'npwp' => 'nullable|string|max:20',
            'no_rekening' => 'nullable|string|max:50',
            'nama' => 'required|string|max:100',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'required|in:L,P',
            'agama' => 'required|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'alamat' => 'nullable|string',
            'kabupaten_kota_id' => 'nullable|exists:kabupaten_kota,id',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'status_perkawinan' => 'required|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati',
            'pendidikan_terakhir' => 'nullable|string|max:50',
            'jurusan_pendidikan' => 'nullable|string|max:100',
            'golongan_id' => 'nullable|exists:golongan,id',
            'jabatan_id' => 'nullable|exists:jabatan,id',
            'unit_kerja_id' => 'nullable|exists:unit_kerja,id',
            'tmt_cpns' => 'nullable|date',
            'tmt_pns' => 'nullable|date',
            'tmt_golongan' => 'nullable|date',
            'tmt_jabatan' => 'nullable|date',
            'status_pegawai' => 'required|in:CPNS,PNS,PPPK,PPPK Paruh Waktu,Non ASN,Berhenti/Keluar,Pensiun',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'keterangan' => 'nullable|string',
        ]);

        $data = $request->except('foto');
        
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('foto-pegawai', 'public');
        }

        $pegawai = Pegawai::create($data);
        $temporaryPassword = Str::random(12);

        // Create user account for pegawai
        User::create([
            'username' => $pegawai->nip,
            'password' => Hash::make($temporaryPassword),
            'role' => 'pegawai',
            'pegawai_id' => $pegawai->id,
            'is_active' => $pegawai->is_active,
            'must_change_password' => true,
        ]);

        return redirect()->route('pegawai.index')->with('success', 'Data pegawai berhasil ditambahkan. Akun login: username = NIP, password sementara = ' . $temporaryPassword . '. Pegawai wajib ganti password saat login pertama.');
    }

    public function show(Pegawai $pegawai)
    {
        // Check if user can access this pegawai
        if (!auth()->user()->canAccessPegawai($pegawai)) {
            return redirect()->route('pegawai.index')->with('error', 'Anda tidak memiliki akses ke pegawai ini.');
        }
        
        $pegawai->load(['golongan', 'jabatan', 'unitKerja', 'kabupatenKota', 'perjalananDinas', 'cuti.jenisCuti', 'kgb']);
        return view('pegawai.show', compact('pegawai'));
    }

    public function edit(Pegawai $pegawai)
    {
        $user = auth()->user();
        
        // Check if user can access this pegawai
        if (!$user->canAccessPegawai($pegawai)) {
            return redirect()->route('pegawai.index')->with('error', 'Anda tidak memiliki akses ke pegawai ini.');
        }
        
        $golongan = Golongan::orderBy('kode')->get();
        $jabatan = Jabatan::orderBy('nama')->get();
        $kabupatenKota = KabupatenKota::orderBy('nama')->get();
        
        // Get distinct eselon values for dropdown
        $eselonList = Jabatan::whereNotNull('eselon')->where('eselon', '!=', '')->distinct()->orderBy('eselon')->pluck('eselon');
        
        // Get selected eselon from pegawai's current jabatan
        $selectedEselon = $pegawai->jabatan ? $pegawai->jabatan->eselon : null;
        
        // Sub admin only sees their own unit kerja
        if ($user->isSubAdmin()) {
            $unitKerja = UnitKerja::where('id', $user->unit_kerja_id)->get();
        } else {
            $unitKerja = UnitKerja::orderBy('nama')->get();
        }
        
        return view('pegawai.edit', compact('pegawai', 'golongan', 'jabatan', 'unitKerja', 'kabupatenKota', 'eselonList', 'selectedEselon'));
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        // Check if user can access this pegawai
        if (!auth()->user()->canAccessPegawai($pegawai)) {
            return redirect()->route('pegawai.index')->with('error', 'Anda tidak memiliki akses ke pegawai ini.');
        }
        
        $request->validate([
            'nip' => 'required|string|max:30|unique:pegawai,nip,' . $pegawai->id,
            'nik' => 'nullable|string|max:16|unique:pegawai,nik,' . $pegawai->id,
            'npwp' => 'nullable|string|max:20',
            'no_rekening' => 'nullable|string|max:50',
            'nama' => 'required|string|max:100',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'required|in:L,P',
            'agama' => 'required|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'alamat' => 'nullable|string',
            'kabupaten_kota_id' => 'nullable|exists:kabupaten_kota,id',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'status_perkawinan' => 'required|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati',
            'pendidikan_terakhir' => 'nullable|string|max:50',
            'jurusan_pendidikan' => 'nullable|string|max:100',
            'golongan_id' => 'nullable|exists:golongan,id',
            'jabatan_id' => 'nullable|exists:jabatan,id',
            'unit_kerja_id' => 'nullable|exists:unit_kerja,id',
            'tmt_cpns' => 'nullable|date',
            'tmt_pns' => 'nullable|date',
            'tmt_golongan' => 'nullable|date',
            'tmt_jabatan' => 'nullable|date',
            'status_pegawai' => 'required|in:CPNS,PNS,PPPK,PPPK Paruh Waktu,Non ASN,Berhenti/Keluar,Pensiun',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'keterangan' => 'nullable|string',
        ]);

        $data = $request->except('foto');
        
        if ($request->hasFile('foto')) {
            // Delete old foto
            if ($pegawai->foto) {
                Storage::disk('public')->delete($pegawai->foto);
            }
            $data['foto'] = $request->file('foto')->store('foto-pegawai', 'public');
        }

        $pegawai->update($data);

        return redirect()->route('pegawai.index')->with('success', 'Data pegawai berhasil diupdate.');
    }

    public function destroy(Pegawai $pegawai)
    {
        // Check if user can access this pegawai
        if (!auth()->user()->canAccessPegawai($pegawai)) {
            return redirect()->route('pegawai.index')->with('error', 'Anda tidak memiliki akses ke pegawai ini.');
        }
        
        if ($pegawai->foto) {
            Storage::disk('public')->delete($pegawai->foto);
        }
        
        $pegawai->delete();
        
        return redirect()->route('pegawai.index')->with('success', 'Data pegawai berhasil dihapus.');
    }

    public function toggleStatus(Pegawai $pegawai)
    {
        // Check if user can access this pegawai
        if (!auth()->user()->canAccessPegawai($pegawai)) {
            return redirect()->route('pegawai.index')->with('error', 'Anda tidak memiliki akses ke pegawai ini.');
        }
        
        $pegawai->is_active = !$pegawai->is_active;
        $pegawai->save();

        // Also update the user account status if exists
        if ($pegawai->user) {
            $pegawai->user->update(['is_active' => $pegawai->is_active]);
        }

        $status = $pegawai->is_active ? 'Aktif' : 'Non-Aktif';
        return redirect()->back()->with('success', "Status pegawai {$pegawai->nama} berhasil diubah menjadi {$status}.");
    }

    public function getJabatanByEselon(Request $request)
    {
        $eselon = $request->get('eselon');
        
        if (empty($eselon)) {
            // Return all jabatan if no eselon selected
            $jabatan = Jabatan::orderBy('nama')->get(['id', 'nama', 'eselon']);
        } else {
            // Filter jabatan by eselon
            $jabatan = Jabatan::where('eselon', $eselon)->orderBy('nama')->get(['id', 'nama', 'eselon']);
        }
        
        return response()->json($jabatan);
    }
}
