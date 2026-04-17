<?php

namespace App\Http\Controllers;

use App\Helpers\IdEncoder;
use App\Models\Jabatan;
use App\Models\KebutuhanPegawai;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetaJabatanController extends Controller
{
    private const EXCLUDED_STATUS_FOR_COUNT = ['Berhenti/Keluar', 'Pensiun'];
    private const UPTD_PUSKESMAS_PREFIX = 'UPTD Puskesmas';
    private const UPTD_PUSKESMAS_SCOPE = '__rekap_uptd_puskesmas__';
    private const DINAS_KESEHATAN_SCOPE = '__rekap_dinas_kesehatan__';
    private const DINAS_KESEHATAN_UNIT_IDS = [1, 2, 3, 4, 5, 6];

    /**
     * Decode mixed URL selection value to supported internal representation.
     */
    private function normalizeUnitKerjaSelection($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = (string) $value;
        if (in_array($value, [self::UPTD_PUSKESMAS_SCOPE, self::DINAS_KESEHATAN_SCOPE], true)) {
            return $value;
        }

        $decodedId = IdEncoder::decode($value);
        if ($decodedId !== null) {
            return $decodedId;
        }

        return is_numeric($value) ? (int) $value : null;
    }

    /**
     * Encode internal unit kerja selection for URL usage.
     */
    private function encodeUnitKerjaSelection($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value) && in_array($value, [self::UPTD_PUSKESMAS_SCOPE, self::DINAS_KESEHATAN_SCOPE], true)) {
            return $value;
        }

        return is_numeric($value) ? IdEncoder::encode((int) $value) : null;
    }

    /**
     * Decode route parameter that should resolve to integer ID.
     */
    private function decodeRouteId($value): ?int
    {
        $value = (string) $value;
        $decodedId = IdEncoder::decode($value);

        if ($decodedId !== null) {
            return $decodedId;
        }

        return is_numeric($value) ? (int) $value : null;
    }

    /**
     * Resolve aggregate scope into unit kerja IDs and labels.
     */
    private function resolveAggregateScope(string $scope): ?array
    {
        if ($scope === self::UPTD_PUSKESMAS_SCOPE) {
            return [
                'ids' => UnitKerja::where('nama', 'like', self::UPTD_PUSKESMAS_PREFIX . '%')->pluck('id'),
                'label' => 'Rekap UPTD Puskesmas',
                'message' => 'Menampilkan rekap total jabatan untuk seluruh unit kerja dengan nama diawali "UPTD Puskesmas".',
            ];
        }

        if ($scope === self::DINAS_KESEHATAN_SCOPE) {
            return [
                'ids' => UnitKerja::whereIn('id', self::DINAS_KESEHATAN_UNIT_IDS)->pluck('id'),
                'label' => 'Rekap Dinas Kesehatan',
                'message' => 'Menampilkan rekap total jabatan untuk unit kerja Dinas Kesehatan (ID 1, 2, 3, 4, 5, dan 6).',
            ];
        }

        return null;
    }

    /**
     * Build recap payload from a unit kerja collection.
     */
    private function buildRekapPayload($unitKerjas): array
    {
        $rekapData = [];

        foreach ($unitKerjas as $unitKerja) {
            $totalBezetting = Pegawai::where('unit_kerja_id', $unitKerja->id)
                ->where('is_active', true)
                ->whereNotIn('status_pegawai', self::EXCLUDED_STATUS_FOR_COUNT)
                ->count();

            $totalKebutuhan = KebutuhanPegawai::where('unit_kerja_id', $unitKerja->id)
                ->sum('jumlah_kebutuhan');

            $selisih = $totalBezetting - $totalKebutuhan;

            $rekapData[] = [
                'unit_kerja' => $unitKerja,
                'bezetting' => $totalBezetting,
                'kebutuhan' => $totalKebutuhan,
                'selisih' => $selisih,
                'status' => $selisih == 0 ? 'Terpenuhi' : ($selisih > 0 ? 'Kelebihan' : 'Kekurangan'),
                'status_class' => $selisih == 0 ? 'success' : ($selisih > 0 ? 'warning' : 'danger'),
            ];
        }

        return [
            'rekapData' => $rekapData,
            'grandTotalBezetting' => array_sum(array_column($rekapData, 'bezetting')),
            'grandTotalKebutuhan' => array_sum(array_column($rekapData, 'kebutuhan')),
        ];
    }

    /**
     * Display peta jabatan per unit kerja
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $uptdPuskesmasScopeValue = self::UPTD_PUSKESMAS_SCOPE;
        $dinasKesehatanScopeValue = self::DINAS_KESEHATAN_SCOPE;
        
        // Jika sub admin, hanya tampilkan unit kerja mereka
        if ($user->isSubAdmin()) {
            $unitKerjas = UnitKerja::where('id', $user->pegawai->unit_kerja_id)->get();
            $selectedUnitKerja = $user->pegawai->unit_kerja_id;
        } else {
            $unitKerjas = UnitKerja::orderBy('nama')->get();
            $selectedUnitKerja = $this->normalizeUnitKerjaSelection($request->get('unit_kerja_id'));
        }

        $selectedUnitKerjaParam = $this->encodeUnitKerjaSelection($selectedUnitKerja);
        
        $petaJabatan = collect();
        $unitKerja = null;
        $selectedUnitKerjaLabel = null;
        $isUptdPuskesmasRecap = false;
        $isDinasKesehatanRecap = false;
        $isAggregatedRecap = false;
        $recapInfoMessage = null;
        $showPetaJabatan = false;
        $totalBezetting = 0;
        $totalKebutuhan = 0;
        
        if ($selectedUnitKerja) {
            $aggregateScope = !$user->isSubAdmin()
                ? $this->resolveAggregateScope((string) $selectedUnitKerja)
                : null;

            if ($aggregateScope) {
                $isAggregatedRecap = true;
                $isUptdPuskesmasRecap = (string) $selectedUnitKerja === self::UPTD_PUSKESMAS_SCOPE;
                $isDinasKesehatanRecap = (string) $selectedUnitKerja === self::DINAS_KESEHATAN_SCOPE;
                $unitKerjaIds = $aggregateScope['ids'];
                $selectedUnitKerjaLabel = $aggregateScope['label'];
                $recapInfoMessage = $aggregateScope['message'];
                $showPetaJabatan = true;

                // Get all jabatan with aggregated kebutuhan for selected aggregate scope
                $jabatans = Jabatan::orderBy('kode')->get();

                foreach ($jabatans as $jabatan) {
                    $jumlahKebutuhan = KebutuhanPegawai::whereIn('unit_kerja_id', $unitKerjaIds)
                        ->where('jabatan_id', $jabatan->id)
                        ->sum('jumlah_kebutuhan');

                    $bezetting = Pegawai::whereIn('unit_kerja_id', $unitKerjaIds)
                        ->where('jabatan_id', $jabatan->id)
                        ->where('is_active', true)
                        ->whereNotIn('status_pegawai', self::EXCLUDED_STATUS_FOR_COUNT)
                        ->count();

                    $selisih = $bezetting - $jumlahKebutuhan;

                    if ($jumlahKebutuhan > 0 || $bezetting > 0) {
                        $petaJabatan->push([
                            'jabatan' => $jabatan,
                            'kebutuhan_id' => null,
                            'bezetting' => $bezetting,
                            'kebutuhan' => $jumlahKebutuhan,
                            'selisih' => $selisih,
                            'status' => $selisih == 0 ? 'Terpenuhi' : ($selisih > 0 ? 'Kelebihan' : 'Kekurangan'),
                            'status_class' => $selisih == 0 ? 'success' : ($selisih > 0 ? 'warning' : 'danger'),
                            'keterangan' => null,
                        ]);

                        $totalBezetting += $bezetting;
                        $totalKebutuhan += $jumlahKebutuhan;
                    }
                }
            } else {
                $unitKerja = UnitKerja::find($selectedUnitKerja);

                if ($unitKerja) {
                    $selectedUnitKerjaLabel = $unitKerja->nama;
                    $showPetaJabatan = true;

                // Get all jabatan with their kebutuhan for this unit kerja
                $jabatans = Jabatan::orderBy('kode')->get();
                
                foreach ($jabatans as $jabatan) {
                    // Get kebutuhan pegawai
                    $kebutuhan = KebutuhanPegawai::where('unit_kerja_id', $selectedUnitKerja)
                        ->where('jabatan_id', $jabatan->id)
                        ->first();
                    
                    // Get bezetting (jumlah pegawai eksisting)
                    $bezetting = Pegawai::where('unit_kerja_id', $selectedUnitKerja)
                        ->where('jabatan_id', $jabatan->id)
                        ->where('is_active', true)
                        ->whereNotIn('status_pegawai', self::EXCLUDED_STATUS_FOR_COUNT)
                        ->count();
                    
                    $jumlahKebutuhan = $kebutuhan ? $kebutuhan->jumlah_kebutuhan : 0;
                    $selisih = $bezetting - $jumlahKebutuhan;
                    
                    // Hanya tampilkan jabatan yang memiliki kebutuhan atau bezetting
                    if ($jumlahKebutuhan > 0 || $bezetting > 0) {
                        $petaJabatan->push([
                            'jabatan' => $jabatan,
                            'kebutuhan_id' => $kebutuhan ? $kebutuhan->id : null,
                            'bezetting' => $bezetting,
                            'kebutuhan' => $jumlahKebutuhan,
                            'selisih' => $selisih,
                            'status' => $selisih == 0 ? 'Terpenuhi' : ($selisih > 0 ? 'Kelebihan' : 'Kekurangan'),
                            'status_class' => $selisih == 0 ? 'success' : ($selisih > 0 ? 'warning' : 'danger'),
                            'keterangan' => $kebutuhan ? $kebutuhan->keterangan : null,
                        ]);
                        
                        $totalBezetting += $bezetting;
                        $totalKebutuhan += $jumlahKebutuhan;
                    }
                }
            }
            }
        }
        
        return view('peta-jabatan.index', compact(
            'unitKerjas',
            'selectedUnitKerja',
            'selectedUnitKerjaParam',
            'unitKerja',
            'selectedUnitKerjaLabel',
            'isUptdPuskesmasRecap',
            'isDinasKesehatanRecap',
            'isAggregatedRecap',
            'recapInfoMessage',
            'showPetaJabatan',
            'uptdPuskesmasScopeValue',
            'dinasKesehatanScopeValue',
            'petaJabatan',
            'totalBezetting',
            'totalKebutuhan'
        ));
    }

    /**
     * Show form to set kebutuhan pegawai per unit kerja
     */
    public function setKebutuhan(Request $request)
    {
        $user = auth()->user();
        
        // Jika sub admin, hanya tampilkan unit kerja mereka
        if ($user->isSubAdmin()) {
            $unitKerjas = UnitKerja::where('id', $user->pegawai->unit_kerja_id)->get();
            $selectedUnitKerja = $user->pegawai->unit_kerja_id;
        } else {
            $unitKerjas = UnitKerja::orderBy('nama')->get();
            $selectedUnitKerja = $this->normalizeUnitKerjaSelection($request->get('unit_kerja_id'));
        }

        $selectedUnitKerjaParam = $this->encodeUnitKerjaSelection($selectedUnitKerja);
        
        $jabatans = Jabatan::orderBy('kode')->get();
        $kebutuhanData = [];
        
        if ($selectedUnitKerja) {
            $kebutuhanRecords = KebutuhanPegawai::where('unit_kerja_id', $selectedUnitKerja)
                ->get()
                ->keyBy('jabatan_id');
            
            foreach ($jabatans as $jabatan) {
                $kebutuhanData[$jabatan->id] = [
                    'jumlah' => $kebutuhanRecords->has($jabatan->id) 
                        ? $kebutuhanRecords[$jabatan->id]->jumlah_kebutuhan 
                        : 0,
                    'keterangan' => $kebutuhanRecords->has($jabatan->id) 
                        ? $kebutuhanRecords[$jabatan->id]->keterangan 
                        : null,
                ];
            }
        }
        
        return view('peta-jabatan.set-kebutuhan', compact(
            'unitKerjas',
            'selectedUnitKerja',
            'selectedUnitKerjaParam',
            'jabatans',
            'kebutuhanData'
        ));
    }

    /**
     * Store kebutuhan pegawai
     */
    public function storeKebutuhan(Request $request)
    {
        $request->validate([
            'unit_kerja_id' => 'required|exists:unit_kerja,id',
            'kebutuhan' => 'required|array',
            'kebutuhan.*.jumlah' => 'nullable|integer|min:0',
            'kebutuhan.*.keterangan' => 'nullable|string|max:255',
        ]);
        
        $user = auth()->user();
        $unitKerjaId = $request->unit_kerja_id;
        
        // Validasi akses sub admin
        if ($user->isSubAdmin() && $user->pegawai->unit_kerja_id != $unitKerjaId) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke unit kerja ini.');
        }
        
        DB::beginTransaction();
        try {
            foreach ($request->kebutuhan as $jabatanId => $data) {
                $jumlah = $data['jumlah'] ?? 0;
                $keterangan = $data['keterangan'] ?? null;
                
                if ($jumlah > 0) {
                    KebutuhanPegawai::updateOrCreate(
                        [
                            'unit_kerja_id' => $unitKerjaId,
                            'jabatan_id' => $jabatanId,
                        ],
                        [
                            'jumlah_kebutuhan' => $jumlah,
                            'keterangan' => $keterangan,
                        ]
                    );
                } else {
                    // Hapus jika jumlah 0
                    KebutuhanPegawai::where('unit_kerja_id', $unitKerjaId)
                        ->where('jabatan_id', $jabatanId)
                        ->delete();
                }
            }
            
            DB::commit();
            return redirect()->route('peta-jabatan.index', ['unit_kerja_id' => $this->encodeUnitKerjaSelection($unitKerjaId)])
                ->with('success', 'Data kebutuhan pegawai berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    /**
     * Show detail pegawai per jabatan di unit kerja
     */
    public function detailPegawai(Request $request, $unitKerjaId, $jabatanId)
    {
        $decodedUnitKerjaId = $this->decodeRouteId($unitKerjaId);
        $decodedJabatanId = $this->decodeRouteId($jabatanId);

        if (!$decodedUnitKerjaId || !$decodedJabatanId) {
            abort(404);
        }

        $unitKerja = UnitKerja::findOrFail($decodedUnitKerjaId);
        $jabatan = Jabatan::findOrFail($decodedJabatanId);
        
        $user = auth()->user();
        
        // Validasi akses sub admin
        if ($user->isSubAdmin() && $user->pegawai->unit_kerja_id != $decodedUnitKerjaId) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke unit kerja ini.');
        }
        
        $pegawais = Pegawai::where('unit_kerja_id', $decodedUnitKerjaId)
            ->where('jabatan_id', $decodedJabatanId)
            ->where('is_active', true)
            ->whereNotIn('status_pegawai', self::EXCLUDED_STATUS_FOR_COUNT)
            ->with(['golongan'])
            ->orderBy('nama')
            ->get();
        
        $kebutuhan = KebutuhanPegawai::where('unit_kerja_id', $decodedUnitKerjaId)
            ->where('jabatan_id', $decodedJabatanId)
            ->first();
        
        return view('peta-jabatan.detail-pegawai', compact(
            'unitKerja',
            'jabatan',
            'pegawais',
            'kebutuhan'
        ));
    }

    /**
     * Export peta jabatan to Excel
     */
    public function export(Request $request)
    {
        $unitKerjaSelection = $this->normalizeUnitKerjaSelection($request->get('unit_kerja_id'));

        if (!$unitKerjaSelection) {
            return redirect()->back()->with('error', 'Pilih unit kerja terlebih dahulu.');
        }

        $isAggregateScope = in_array((string) $unitKerjaSelection, [self::UPTD_PUSKESMAS_SCOPE, self::DINAS_KESEHATAN_SCOPE], true);
        $user = auth()->user();
        if ($isAggregateScope && $user->isSubAdmin()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke rekap agregat ini.');
        }

        $aggregateScope = $this->resolveAggregateScope((string) $unitKerjaSelection);
        $isAggregatedRecap = (bool) $aggregateScope;
        $unitKerjaId = $isAggregatedRecap ? null : (int) $unitKerjaSelection;

        $unitKerja = $isAggregatedRecap
            ? (object) ['nama' => $aggregateScope['label']]
            : UnitKerja::findOrFail($unitKerjaId);

        $aggregateUnitKerjaIds = $isAggregatedRecap ? $aggregateScope['ids'] : collect();

        // Generate data for export
        $data = [];
        $jabatans = Jabatan::orderBy('kode')->get();

        foreach ($jabatans as $jabatan) {
            if ($isAggregatedRecap) {
                $jumlahKebutuhan = KebutuhanPegawai::whereIn('unit_kerja_id', $aggregateUnitKerjaIds)
                    ->where('jabatan_id', $jabatan->id)
                    ->sum('jumlah_kebutuhan');

                $bezetting = Pegawai::whereIn('unit_kerja_id', $aggregateUnitKerjaIds)
                    ->where('jabatan_id', $jabatan->id)
                    ->where('is_active', true)
                    ->whereNotIn('status_pegawai', self::EXCLUDED_STATUS_FOR_COUNT)
                    ->count();

                $keterangan = '';
            } else {
                $kebutuhan = KebutuhanPegawai::where('unit_kerja_id', $unitKerjaId)
                    ->where('jabatan_id', $jabatan->id)
                    ->first();

                $bezetting = Pegawai::where('unit_kerja_id', $unitKerjaId)
                    ->where('jabatan_id', $jabatan->id)
                    ->where('is_active', true)
                    ->whereNotIn('status_pegawai', self::EXCLUDED_STATUS_FOR_COUNT)
                    ->count();

                $jumlahKebutuhan = $kebutuhan ? $kebutuhan->jumlah_kebutuhan : 0;
                $keterangan = $kebutuhan ? $kebutuhan->keterangan : '';
            }

            if ($jumlahKebutuhan > 0 || $bezetting > 0) {
                $selisih = $bezetting - $jumlahKebutuhan;
                $data[] = [
                    'kode' => $jabatan->kode,
                    'nama' => $jabatan->nama,
                    'bezetting' => $bezetting,
                    'kebutuhan' => $jumlahKebutuhan,
                    'selisih' => $selisih,
                    'status' => $selisih == 0 ? 'Terpenuhi' : ($selisih > 0 ? 'Kelebihan' : 'Kekurangan'),
                    'keterangan' => $keterangan,
                ];
            }
        }

        $scopeName = strtolower((string) $unitKerja->nama);
        $scopeName = preg_replace('/[^a-z0-9]+/i', '_', $scopeName);
        $scopeName = trim((string) $scopeName, '_');
        if ($scopeName === '') {
            $scopeName = 'unit_kerja';
        }

        $filename = 'peta_jabatan_' . $scopeName . '_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($unitKerja, $data) {
            $file = fopen('php://output', 'w');

            // BOM UTF-8 agar karakter tampil benar di Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['Peta Jabatan']);
            fputcsv($file, ['Unit Kerja', $unitKerja->nama]);
            fputcsv($file, ['Tanggal Export', now()->format('d/m/Y H:i')]);
            fputcsv($file, []);

            fputcsv($file, ['No', 'Kode', 'Nama Jabatan', 'Bezetting', 'Kebutuhan', 'Selisih', 'Status', 'Keterangan']);

            foreach ($data as $index => $item) {
                fputcsv($file, [
                    $index + 1,
                    $item['kode'],
                    $item['nama'],
                    $item['bezetting'],
                    $item['kebutuhan'],
                    $item['selisih'],
                    $item['status'],
                    $item['keterangan'],
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Print peta jabatan
     */
    public function print(Request $request)
    {
        $unitKerjaSelection = $this->normalizeUnitKerjaSelection($request->get('unit_kerja_id'));
        
        if (!$unitKerjaSelection) {
            return redirect()->back()->with('error', 'Pilih unit kerja terlebih dahulu.');
        }

        $isAggregateScope = in_array((string) $unitKerjaSelection, [self::UPTD_PUSKESMAS_SCOPE, self::DINAS_KESEHATAN_SCOPE], true);
        $user = auth()->user();
        if ($isAggregateScope && $user->isSubAdmin()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke rekap agregat ini.');
        }

        $aggregateScope = $this->resolveAggregateScope((string) $unitKerjaSelection);
        $isAggregatedRecap = (bool) $aggregateScope;
        $unitKerjaId = $isAggregatedRecap ? null : (int) $unitKerjaSelection;

        $unitKerja = $isAggregatedRecap
            ? (object) ['nama' => $aggregateScope['label']]
            : UnitKerja::findOrFail($unitKerjaId);
        
        // Generate data for print
        $data = [];
        $totalBezetting = 0;
        $totalKebutuhan = 0;
        $jabatans = Jabatan::orderBy('kode')->get();
        $aggregateUnitKerjaIds = $isAggregatedRecap ? $aggregateScope['ids'] : collect();
        
        foreach ($jabatans as $jabatan) {
            if ($isAggregatedRecap) {
                $jumlahKebutuhan = KebutuhanPegawai::whereIn('unit_kerja_id', $aggregateUnitKerjaIds)
                    ->where('jabatan_id', $jabatan->id)
                    ->sum('jumlah_kebutuhan');

                $bezetting = Pegawai::whereIn('unit_kerja_id', $aggregateUnitKerjaIds)
                    ->where('jabatan_id', $jabatan->id)
                    ->where('is_active', true)
                    ->whereNotIn('status_pegawai', self::EXCLUDED_STATUS_FOR_COUNT)
                    ->count();

                $keterangan = '';
            } else {
                $kebutuhan = KebutuhanPegawai::where('unit_kerja_id', $unitKerjaId)
                    ->where('jabatan_id', $jabatan->id)
                    ->first();

                $bezetting = Pegawai::where('unit_kerja_id', $unitKerjaId)
                    ->where('jabatan_id', $jabatan->id)
                    ->where('is_active', true)
                    ->whereNotIn('status_pegawai', self::EXCLUDED_STATUS_FOR_COUNT)
                    ->count();

                $jumlahKebutuhan = $kebutuhan ? $kebutuhan->jumlah_kebutuhan : 0;
                $keterangan = $kebutuhan ? $kebutuhan->keterangan : '';
            }
            
            if ($jumlahKebutuhan > 0 || $bezetting > 0) {
                $selisih = $bezetting - $jumlahKebutuhan;
                $data[] = [
                    'kode' => $jabatan->kode,
                    'nama' => $jabatan->nama,
                    'bezetting' => $bezetting,
                    'kebutuhan' => $jumlahKebutuhan,
                    'selisih' => $selisih,
                    'status' => $selisih == 0 ? 'Terpenuhi' : ($selisih > 0 ? 'Kelebihan' : 'Kekurangan'),
                    'keterangan' => $keterangan,
                ];
                
                $totalBezetting += $bezetting;
                $totalKebutuhan += $jumlahKebutuhan;
            }
        }
        
        return view('peta-jabatan.print', compact('unitKerja', 'data', 'totalBezetting', 'totalKebutuhan'));
    }

    /**
     * Rekap peta jabatan semua unit kerja
     */
    public function rekap()
    {
        $user = auth()->user();
        
        if ($user->isSubAdmin()) {
            return redirect()->route('peta-jabatan.index')
                ->with('error', 'Anda tidak memiliki akses ke halaman rekap.');
        }
        
        $unitKerjas = UnitKerja::orderBy('nama')->get();
        $payload = $this->buildRekapPayload($unitKerjas);
        $rekapTitle = 'Rekap Peta Jabatan Semua Unit Kerja';
        $rekapBadge = 'Semua Unit';
        $rekapScope = 'all';

        return view('peta-jabatan.rekap', array_merge($payload, compact('rekapTitle', 'rekapBadge', 'rekapScope')));
    }

    /**
     * Rekap peta jabatan khusus unit kerja UPTD Puskesmas.
     */
    public function rekapUptdPuskesmas()
    {
        $user = auth()->user();

        if ($user->isSubAdmin()) {
            return redirect()->route('peta-jabatan.index')
                ->with('error', 'Anda tidak memiliki akses ke halaman rekap.');
        }

        $unitKerjas = UnitKerja::where('nama', 'like', self::UPTD_PUSKESMAS_PREFIX . '%')
            ->orderBy('nama')
            ->get();

        $payload = $this->buildRekapPayload($unitKerjas);
        $rekapTitle = 'Rekap Unit Kerja UPTD Puskesmas';
        $rekapBadge = self::UPTD_PUSKESMAS_PREFIX;
        $rekapScope = 'uptd-puskesmas';

        return view('peta-jabatan.rekap', array_merge($payload, compact('rekapTitle', 'rekapBadge', 'rekapScope')));
    }

    /**
     * Print rekap peta jabatan by scope.
     */
    public function printRekap(Request $request)
    {
        $user = auth()->user();

        if ($user->isSubAdmin()) {
            return redirect()->route('peta-jabatan.index')
                ->with('error', 'Anda tidak memiliki akses ke halaman rekap.');
        }

        $scope = $request->get('scope', 'all');
        $rekapScope = 'all';

        if ($scope === 'uptd-puskesmas') {
            $unitKerjas = UnitKerja::where('nama', 'like', self::UPTD_PUSKESMAS_PREFIX . '%')
                ->orderBy('nama')
                ->get();
            $rekapTitle = 'Rekap Unit Kerja UPTD Puskesmas';
            $rekapBadge = self::UPTD_PUSKESMAS_PREFIX;
            $rekapScope = 'uptd-puskesmas';
        } else {
            $unitKerjas = UnitKerja::orderBy('nama')->get();
            $rekapTitle = 'Rekap Peta Jabatan Semua Unit Kerja';
            $rekapBadge = 'Semua Unit';
        }

        $payload = $this->buildRekapPayload($unitKerjas);

        return view('peta-jabatan.print-rekap', array_merge($payload, compact('rekapTitle', 'rekapBadge', 'rekapScope')));
    }
}
