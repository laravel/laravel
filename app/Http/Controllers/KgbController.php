<?php

namespace App\Http\Controllers;

use App\Models\Kgb;
use App\Models\Pegawai;
use App\Models\Golongan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KgbController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Kgb::with(['pegawai', 'golonganLama', 'golonganBaru']);
        
        if ($user->isPegawai()) {
            $query->where('pegawai_id', $user->pegawai_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_sk', 'like', "%{$search}%")
                  ->orWhereHas('pegawai', function($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%");
                  });
            });
        }
        
        $kgb = $query->latest()->paginate(10)->withQueryString();
        
        return view('kgb.index', compact('kgb'));
    }

    public function create()
    {
        $pegawai = Pegawai::with('golongan')
                          ->where('is_active', true)
                          ->orderBy('nama')
                          ->get();
        $golongan = Golongan::orderBy('kode')->get();
        
        return view('kgb.create', compact('pegawai', 'golongan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'tmt_kgb' => 'required|date',
            'golongan_baru_id' => 'required|exists:golongan,id',
            'gaji_pokok_baru' => 'required|numeric|min:0',
            'masa_kerja_tahun' => 'required|integer|min:0',
            'masa_kerja_bulan' => 'required|integer|min:0|max:11',
            'keterangan' => 'nullable|string',
        ]);

        $pegawai = Pegawai::with('golongan')->find($request->pegawai_id);
        
        // Calculate TMT KGB berikutnya (2 years from current TMT KGB)
        $tmtKgb = Carbon::parse($request->tmt_kgb);
        $tmtKgbBerikutnya = $tmtKgb->copy()->addYears(2);

        $data = $request->all();
        $data['nomor_sk'] = Kgb::generateNomorSK();
        $data['tmt_kgb_berikutnya'] = $tmtKgbBerikutnya;
        $data['golongan_lama_id'] = $pegawai->golongan_id;
        $data['gaji_pokok_lama'] = $pegawai->golongan ? $pegawai->golongan->gaji_pokok : 0;
        $data['status'] = 'Diproses';

        Kgb::create($data);

        return redirect()->route('kgb.index')->with('success', 'Pengajuan KGB berhasil dibuat.');
    }

    public function show(Kgb $kgb)
    {
        $user = auth()->user();
        
        // Check access
        if ($user->isPegawai() && $kgb->pegawai_id != $user->pegawai_id) {
            return redirect()->route('kgb.index')->with('error', 'Anda tidak memiliki akses.');
        }
        
        $kgb->load(['pegawai.jabatan', 'pegawai.unitKerja', 'golonganLama', 'golonganBaru', 'approver']);
        
        return view('kgb.show', compact('kgb'));
    }

    public function edit(Kgb $kgb)
    {
        // Only allow edit if status is 'Diproses'
        if ($kgb->status !== 'Diproses') {
            return redirect()->route('kgb.index')->with('error', 'KGB tidak dapat diedit.');
        }
        
        $pegawai = Pegawai::with('golongan')
                          ->where('is_active', true)
                          ->orderBy('nama')
                          ->get();
        $golongan = Golongan::orderBy('kode')->get();
        
        return view('kgb.edit', compact('kgb', 'pegawai', 'golongan'));
    }

    public function update(Request $request, Kgb $kgb)
    {
        // Only allow edit if status is 'Diproses'
        if ($kgb->status !== 'Diproses') {
            return redirect()->route('kgb.index')->with('error', 'KGB tidak dapat diedit.');
        }

        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'tmt_kgb' => 'required|date',
            'golongan_baru_id' => 'required|exists:golongan,id',
            'gaji_pokok_baru' => 'required|numeric|min:0',
            'masa_kerja_tahun' => 'required|integer|min:0',
            'masa_kerja_bulan' => 'required|integer|min:0|max:11',
            'keterangan' => 'nullable|string',
        ]);

        $pegawai = Pegawai::with('golongan')->find($request->pegawai_id);
        
        // Calculate TMT KGB berikutnya (2 years from current TMT KGB)
        $tmtKgb = Carbon::parse($request->tmt_kgb);
        $tmtKgbBerikutnya = $tmtKgb->copy()->addYears(2);

        $data = $request->all();
        $data['tmt_kgb_berikutnya'] = $tmtKgbBerikutnya;
        $data['golongan_lama_id'] = $pegawai->golongan_id;
        $data['gaji_pokok_lama'] = $pegawai->golongan ? $pegawai->golongan->gaji_pokok : 0;

        $kgb->update($data);

        return redirect()->route('kgb.index')->with('success', 'Data KGB berhasil diupdate.');
    }

    public function destroy(Kgb $kgb)
    {
        // Only allow delete if status is 'Diproses'
        if ($kgb->status !== 'Diproses') {
            return redirect()->route('kgb.index')->with('error', 'KGB tidak dapat dihapus.');
        }
        
        $kgb->delete();
        
        return redirect()->route('kgb.index')->with('success', 'Data KGB berhasil dihapus.');
    }

    public function approve(Request $request, Kgb $kgb)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'keterangan' => 'nullable|string',
        ]);

        $status = $request->action === 'approve' ? 'Disetujui' : 'Ditolak';

        $kgb->update([
            'status' => $status,
            'keterangan' => $request->keterangan,
            'disetujui_oleh' => auth()->id(),
            'tanggal_disetujui' => now(),
        ]);

        // Update pegawai golongan if approved
        if ($status === 'Disetujui') {
            $kgb->pegawai->update([
                'golongan_id' => $kgb->golongan_baru_id,
                'tmt_golongan' => $kgb->tmt_kgb,
            ]);
        }

        return redirect()->route('kgb.index')->with('success', "KGB telah {$status}.");
    }

    public function getPegawaiInfo(Pegawai $pegawai)
    {
        $pegawai->load('golongan');

        // Calculate masa kerja
        $masaKerja = ['tahun' => 0, 'bulan' => 0];
        if ($pegawai->tmt_pns) {
            $tmt = Carbon::parse($pegawai->tmt_pns);
            $now = Carbon::now();
            $diff = $tmt->diff($now);
            $masaKerja = ['tahun' => $diff->y, 'bulan' => $diff->m];
        }

        return response()->json([
            'pegawai' => $pegawai,
            'golongan' => $pegawai->golongan,
            'masa_kerja' => $masaKerja,
        ]);
    }
}
