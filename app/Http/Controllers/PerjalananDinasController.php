<?php

namespace App\Http\Controllers;

use App\Models\PerjalananDinas;
use App\Models\Pegawai;
use Illuminate\Http\Request;

class PerjalananDinasController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = PerjalananDinas::with('pegawai');
        
        if ($user->isPegawai()) {
            $query->where('pegawai_id', $user->pegawai_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhere('tujuan', 'like', "%{$search}%")
                  ->orWhereHas('pegawai', function($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  });
            });
        }
        
        $perjalananDinas = $query->latest()->paginate(10)->withQueryString();
        
        return view('perjalanan-dinas.index', compact('perjalananDinas'));
    }

    public function create()
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            $pegawai = Pegawai::where('is_active', true)->orderBy('nama')->get();
        } else {
            $pegawai = collect([$user->pegawai]);
        }
        
        return view('perjalanan-dinas.create', compact('pegawai'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'tanggal_berangkat' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_berangkat',
            'tujuan' => 'required|string|max:200',
            'maksud_perjalanan' => 'required|string',
            'jenis_transportasi' => 'required|in:Darat,Laut,Udara',
            'biaya_transport' => 'nullable|numeric|min:0',
            'biaya_penginapan' => 'nullable|numeric|min:0',
            'uang_harian' => 'nullable|numeric|min:0',
            'biaya_lainnya' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        // Verify pegawai ownership for non-admin
        if ($user->isPegawai() && $request->pegawai_id != $user->pegawai_id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk pegawai ini.');
        }

        $data = $request->all();
        $data['nomor_surat'] = PerjalananDinas::generateNomorSurat();
        $data['total_biaya'] = ($request->biaya_transport ?? 0) + 
                              ($request->biaya_penginapan ?? 0) + 
                              ($request->uang_harian ?? 0) + 
                              ($request->biaya_lainnya ?? 0);
        $data['status'] = 'Diajukan';

        PerjalananDinas::create($data);

        return redirect()->route('perjalanan-dinas.index')->with('success', 'Pengajuan perjalanan dinas berhasil dibuat.');
    }

    public function show(PerjalananDinas $perjalananDina)
    {
        $user = auth()->user();
        
        // Check access
        if ($user->isPegawai() && $perjalananDina->pegawai_id != $user->pegawai_id) {
            return redirect()->route('perjalanan-dinas.index')->with('error', 'Anda tidak memiliki akses.');
        }
        
        $perjalananDina->load(['pegawai.golongan', 'pegawai.jabatan', 'pegawai.unitKerja', 'approver']);
        
        return view('perjalanan-dinas.show', ['perjalananDinas' => $perjalananDina]);
    }

    public function edit(PerjalananDinas $perjalananDina)
    {
        $user = auth()->user();
        
        // Check access
        if ($user->isPegawai() && $perjalananDina->pegawai_id != $user->pegawai_id) {
            return redirect()->route('perjalanan-dinas.index')->with('error', 'Anda tidak memiliki akses.');
        }
        
        // Only allow edit if status is 'Diajukan'
        if ($perjalananDina->status !== 'Diajukan') {
            return redirect()->route('perjalanan-dinas.index')->with('error', 'Perjalanan dinas tidak dapat diedit.');
        }
        
        if ($user->isAdmin()) {
            $pegawai = Pegawai::where('is_active', true)->orderBy('nama')->get();
        } else {
            $pegawai = collect([$user->pegawai]);
        }
        
        return view('perjalanan-dinas.edit', ['perjalananDinas' => $perjalananDina, 'pegawai' => $pegawai]);
    }

    public function update(Request $request, PerjalananDinas $perjalananDina)
    {
        $user = auth()->user();
        
        // Check access
        if ($user->isPegawai() && $perjalananDina->pegawai_id != $user->pegawai_id) {
            return redirect()->route('perjalanan-dinas.index')->with('error', 'Anda tidak memiliki akses.');
        }
        
        // Only allow edit if status is 'Diajukan'
        if ($perjalananDina->status !== 'Diajukan') {
            return redirect()->route('perjalanan-dinas.index')->with('error', 'Perjalanan dinas tidak dapat diedit.');
        }

        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'tanggal_berangkat' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_berangkat',
            'tujuan' => 'required|string|max:200',
            'maksud_perjalanan' => 'required|string',
            'jenis_transportasi' => 'required|in:Darat,Laut,Udara',
            'biaya_transport' => 'nullable|numeric|min:0',
            'biaya_penginapan' => 'nullable|numeric|min:0',
            'uang_harian' => 'nullable|numeric|min:0',
            'biaya_lainnya' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['total_biaya'] = ($request->biaya_transport ?? 0) + 
                              ($request->biaya_penginapan ?? 0) + 
                              ($request->uang_harian ?? 0) + 
                              ($request->biaya_lainnya ?? 0);

        $perjalananDina->update($data);

        return redirect()->route('perjalanan-dinas.index')->with('success', 'Data perjalanan dinas berhasil diupdate.');
    }

    public function destroy(PerjalananDinas $perjalananDina)
    {
        $user = auth()->user();
        
        // Check access
        if ($user->isPegawai() && $perjalananDina->pegawai_id != $user->pegawai_id) {
            return redirect()->route('perjalanan-dinas.index')->with('error', 'Anda tidak memiliki akses.');
        }
        
        // Only allow delete if status is 'Diajukan'
        if ($perjalananDina->status !== 'Diajukan') {
            return redirect()->route('perjalanan-dinas.index')->with('error', 'Perjalanan dinas tidak dapat dihapus.');
        }
        
        $perjalananDina->delete();
        
        return redirect()->route('perjalanan-dinas.index')->with('success', 'Data perjalanan dinas berhasil dihapus.');
    }

    public function approve(Request $request, PerjalananDinas $perjalananDina)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'keterangan' => 'nullable|string',
        ]);

        $status = $request->action === 'approve' ? 'Disetujui' : 'Ditolak';

        $perjalananDina->update([
            'status' => $status,
            'keterangan' => $request->keterangan,
            'disetujui_oleh' => auth()->id(),
            'tanggal_disetujui' => now(),
        ]);

        return redirect()->route('perjalanan-dinas.index')->with('success', "Perjalanan dinas telah {$status}.");
    }

    public function selesai(PerjalananDinas $perjalananDina)
    {
        if ($perjalananDina->status !== 'Disetujui') {
            return redirect()->route('perjalanan-dinas.index')->with('error', 'Perjalanan dinas belum disetujui.');
        }

        $perjalananDina->update(['status' => 'Selesai']);

        return redirect()->route('perjalanan-dinas.index')->with('success', 'Perjalanan dinas telah selesai.');
    }
}
