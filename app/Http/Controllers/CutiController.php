<?php

namespace App\Http\Controllers;

use App\Helpers\IdEncoder;
use App\Models\Cuti;
use App\Models\JenisCuti;
use App\Models\Pegawai;
use App\Models\SaldoCuti;
use Illuminate\Http\Request;

class CutiController extends Controller
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
        $selectedJenisCutiId = $this->decodeFilterId($request->get('jenis_cuti_id'));
        
        $query = Cuti::with(['pegawai', 'jenisCuti']);
        
        if ($user->isPegawai()) {
            $query->where('pegawai_id', $user->pegawai_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis_cuti_id') && $selectedJenisCutiId) {
            $query->where('jenis_cuti_id', $selectedJenisCutiId);
        }

        if ($request->filled('bulan')) {
            $query->whereRaw("DATE_FORMAT(tanggal_mulai, '%Y-%m') = ?", [$request->bulan]);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhereHas('pegawai', function($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $jenisCuti = JenisCuti::orderBy('nama')->get();
        $selectedJenisCutiParam = $this->encodeFilterId($selectedJenisCutiId);
        
        $cuti = $query->latest()->paginate(10)->withQueryString();
        
        return view('cuti.index', compact('cuti', 'jenisCuti', 'selectedJenisCutiParam'));
    }

    public function create()
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            $pegawai = Pegawai::where('is_active', true)->orderBy('nama')->get();
        } else {
            $pegawai = collect([$user->pegawai]);
        }
        
        $jenisCuti = JenisCuti::orderBy('nama')->get();
        
        return view('cuti.create', compact('pegawai', 'jenisCuti'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'jenis_cuti_id' => 'required|exists:jenis_cuti,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string',
            'alamat_selama_cuti' => 'nullable|string',
            'telepon_darurat' => 'nullable|string|max:20',
            'keterangan' => 'nullable|string',
        ]);

        // Verify pegawai ownership for non-admin
        if ($user->isPegawai() && $request->pegawai_id != $user->pegawai_id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk pegawai ini.');
        }

        // Calculate number of days
        $tanggalMulai = \Carbon\Carbon::parse($request->tanggal_mulai);
        $tanggalSelesai = \Carbon\Carbon::parse($request->tanggal_selesai);
        $jumlahHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

        // Check saldo cuti
        $jenisCuti = JenisCuti::find($request->jenis_cuti_id);
        $saldoCuti = SaldoCuti::where('pegawai_id', $request->pegawai_id)
                              ->where('jenis_cuti_id', $request->jenis_cuti_id)
                              ->where('tahun', date('Y'))
                              ->first();

        if ($saldoCuti && $saldoCuti->saldo_sisa < $jumlahHari) {
            return redirect()->back()->with('error', "Saldo cuti tidak mencukupi. Sisa: {$saldoCuti->saldo_sisa} hari.")->withInput();
        }

        $data = $request->all();
        $data['nomor_surat'] = Cuti::generateNomorSurat();
        $data['jumlah_hari'] = $jumlahHari;
        $data['status'] = 'Diajukan';

        Cuti::create($data);

        return redirect()->route('cuti.index')->with('success', 'Pengajuan cuti berhasil dibuat.');
    }

    public function show(Cuti $cuti)
    {
        $user = auth()->user();
        
        // Check access
        if ($user->isPegawai() && $cuti->pegawai_id != $user->pegawai_id) {
            return redirect()->route('cuti.index')->with('error', 'Anda tidak memiliki akses.');
        }
        
        $cuti->load(['pegawai.golongan', 'pegawai.jabatan', 'pegawai.unitKerja', 'jenisCuti', 'approver']);
        
        // Get saldo cuti
        $saldoCuti = SaldoCuti::where('pegawai_id', $cuti->pegawai_id)
                              ->where('jenis_cuti_id', $cuti->jenis_cuti_id)
                              ->where('tahun', date('Y'))
                              ->first();
        
        return view('cuti.show', compact('cuti', 'saldoCuti'));
    }

    public function edit(Cuti $cuti)
    {
        $user = auth()->user();
        
        // Check access
        if ($user->isPegawai() && $cuti->pegawai_id != $user->pegawai_id) {
            return redirect()->route('cuti.index')->with('error', 'Anda tidak memiliki akses.');
        }
        
        // Only allow edit if status is 'Diajukan'
        if ($cuti->status !== 'Diajukan') {
            return redirect()->route('cuti.index')->with('error', 'Cuti tidak dapat diedit.');
        }
        
        if ($user->isAdmin()) {
            $pegawai = Pegawai::where('is_active', true)->orderBy('nama')->get();
        } else {
            $pegawai = collect([$user->pegawai]);
        }
        
        $jenisCuti = JenisCuti::orderBy('nama')->get();
        
        return view('cuti.edit', compact('cuti', 'pegawai', 'jenisCuti'));
    }

    public function update(Request $request, Cuti $cuti)
    {
        $user = auth()->user();
        
        // Check access
        if ($user->isPegawai() && $cuti->pegawai_id != $user->pegawai_id) {
            return redirect()->route('cuti.index')->with('error', 'Anda tidak memiliki akses.');
        }
        
        // Only allow edit if status is 'Diajukan'
        if ($cuti->status !== 'Diajukan') {
            return redirect()->route('cuti.index')->with('error', 'Cuti tidak dapat diedit.');
        }

        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'jenis_cuti_id' => 'required|exists:jenis_cuti,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string',
            'alamat_selama_cuti' => 'nullable|string',
            'telepon_darurat' => 'nullable|string|max:20',
            'keterangan' => 'nullable|string',
        ]);

        // Calculate number of days
        $tanggalMulai = \Carbon\Carbon::parse($request->tanggal_mulai);
        $tanggalSelesai = \Carbon\Carbon::parse($request->tanggal_selesai);
        $jumlahHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

        $data = $request->all();
        $data['jumlah_hari'] = $jumlahHari;

        $cuti->update($data);

        return redirect()->route('cuti.index')->with('success', 'Data cuti berhasil diupdate.');
    }

    public function destroy(Cuti $cuti)
    {
        $user = auth()->user();
        
        // Check access
        if ($user->isPegawai() && $cuti->pegawai_id != $user->pegawai_id) {
            return redirect()->route('cuti.index')->with('error', 'Anda tidak memiliki akses.');
        }
        
        // Only allow delete if status is 'Diajukan'
        if ($cuti->status !== 'Diajukan') {
            return redirect()->route('cuti.index')->with('error', 'Cuti tidak dapat dihapus.');
        }
        
        $cuti->delete();
        
        return redirect()->route('cuti.index')->with('success', 'Data cuti berhasil dihapus.');
    }

    public function approve(Request $request, Cuti $cuti)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'keterangan' => 'nullable|string',
        ]);

        $status = $request->action === 'approve' ? 'Disetujui' : 'Ditolak';

        $cuti->update([
            'status' => $status,
            'keterangan' => $request->keterangan,
            'disetujui_oleh' => auth()->id(),
            'tanggal_disetujui' => now(),
        ]);

        // Update saldo cuti if approved
        if ($status === 'Disetujui') {
            $saldoCuti = SaldoCuti::firstOrCreate(
                [
                    'pegawai_id' => $cuti->pegawai_id,
                    'jenis_cuti_id' => $cuti->jenis_cuti_id,
                    'tahun' => date('Y'),
                ],
                [
                    'saldo_awal' => $cuti->jenisCuti->max_hari,
                    'saldo_terpakai' => 0,
                    'saldo_sisa' => $cuti->jenisCuti->max_hari,
                ]
            );

            $saldoCuti->saldo_terpakai += $cuti->jumlah_hari;
            $saldoCuti->saldo_sisa = $saldoCuti->saldo_awal - $saldoCuti->saldo_terpakai;
            $saldoCuti->save();
        }

        return redirect()->route('cuti.index')->with('success', "Cuti telah {$status}.");
    }

    public function selesai(Cuti $cuti)
    {
        if ($cuti->status !== 'Disetujui') {
            return redirect()->route('cuti.index')->with('error', 'Cuti belum disetujui.');
        }

        $cuti->update(['status' => 'Selesai']);

        return redirect()->route('cuti.index')->with('success', 'Cuti telah selesai.');
    }

    public function saldo()
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            $saldoCuti = SaldoCuti::with(['pegawai', 'jenisCuti'])
                                   ->where('tahun', date('Y'))
                                   ->paginate(10);
        } else {
            $saldoCuti = SaldoCuti::with(['jenisCuti'])
                                   ->where('pegawai_id', $user->pegawai_id)
                                   ->where('tahun', date('Y'))
                                   ->paginate(10);
        }
        
        return view('cuti.saldo', compact('saldoCuti'));
    }
}
