<?php

namespace App\Http\Controllers;

use App\Helpers\IdEncoder;
use App\Models\Pegawai;
use App\Models\Golongan;
use App\Models\Jabatan;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Status pegawai yang tidak dihitung pada rekap jabatan.
     */
    private const EXCLUDED_STATUS_FOR_COUNT = ['Berhenti/Keluar', 'Pensiun'];
    private const UPTD_PUSKESMAS_PREFIX = 'UPTD Puskesmas';

    /**
     * Get unit_kerja_id for filtering based on user role
     */
    private function getUnitKerjaFilter()
    {
        $user = auth()->user();
        if ($user->isSubAdmin()) {
            return $user->unit_kerja_id;
        }
        return null;
    }

    /**
     * Apply unit_kerja filter to query
     */
    private function applyUnitKerjaFilter($query, $unitKerjaId = null)
    {
        $filterUnitKerja = $unitKerjaId ?? $this->getUnitKerjaFilter();
        if ($filterUnitKerja) {
            $query->where('unit_kerja_id', $filterUnitKerja);
        }
        return $query;
    }

    /**
     * Exclude inactive statuses from aggregate calculations.
     */
    private function applyExcludedStatusFilter($query, $column = 'status_pegawai')
    {
        $query->whereNotIn($column, self::EXCLUDED_STATUS_FOR_COUNT);
        return $query;
    }

    /**
     * Status list that should be included in active aggregate reports.
     */
    private function activeStatusList(): array
    {
        return ['PNS', 'CPNS', 'PPPK', 'PPPK Paruh Waktu', 'Non ASN'];
    }

    /**
     * Decode encrypted filter ID with numeric fallback.
     */
    private function decodeFilterId($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = (string) $value;
        $decodedId = IdEncoder::decode($value);

        if ($decodedId !== null) {
            return $decodedId;
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

    /**
     * Halaman utama laporan
     */
    public function index()
    {
        $unitKerjaFilter = $this->getUnitKerjaFilter();
        $user = auth()->user();
        
        $pegawaiQuery = Pegawai::query();
        if ($unitKerjaFilter) {
            $pegawaiQuery->where('unit_kerja_id', $unitKerjaFilter);
        }
        $this->applyExcludedStatusFilter($pegawaiQuery);
        
        $stats = [
            'total_pegawai' => (clone $pegawaiQuery)->count(),
            'total_golongan' => Golongan::count(),
            'total_jabatan' => Jabatan::count(),
            'total_unit_kerja' => $unitKerjaFilter ? 1 : UnitKerja::count(),
            'pns' => (clone $pegawaiQuery)->where('status_pegawai', 'PNS')->count(),
            'cpns' => (clone $pegawaiQuery)->where('status_pegawai', 'CPNS')->count(),
            'pppk' => (clone $pegawaiQuery)->where('status_pegawai', 'PPPK')->count(),
            'pppk_paruh_waktu' => (clone $pegawaiQuery)->where('status_pegawai', 'PPPK Paruh Waktu')->count(),
            'non_asn' => (clone $pegawaiQuery)->where('status_pegawai', 'Non ASN')->count(),
            'berhenti_keluar' => 0,
            'pensiun' => 0,
            'laki_laki' => (clone $pegawaiQuery)->where('jenis_kelamin', 'L')->count(),
            'perempuan' => (clone $pegawaiQuery)->where('jenis_kelamin', 'P')->count(),
        ];
        
        $unitKerja = $user->isSubAdmin() ? $user->unitKerja : null;

        return view('laporan.index', compact('stats', 'unitKerja'));
    }

    /**
     * Laporan data pegawai
     */
    public function pegawai(Request $request)
    {
        $query = Pegawai::with(['golongan', 'jabatan', 'unitKerja']);
        $selectedGolonganId = $this->decodeFilterId($request->get('golongan_id'));
        $selectedJabatanId = $this->decodeFilterId($request->get('jabatan_id'));
        $selectedUnitKerjaId = $this->decodeFilterId($request->get('unit_kerja_id'));
        
        // Apply unit kerja filter for sub admin
        $this->applyUnitKerjaFilter($query);

        // Filter berdasarkan status
        if ($request->filled('status_pegawai')) {
            $query->where('status_pegawai', $request->status_pegawai);
        }

        // Filter berdasarkan jenis kelamin
        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        // Filter berdasarkan golongan
        if ($request->filled('golongan_id')) {
            if ($selectedGolonganId) {
                $query->where('golongan_id', $selectedGolonganId);
            }
        }

        // Filter berdasarkan jabatan
        if ($request->filled('jabatan_id')) {
            if ($selectedJabatanId) {
                $query->where('jabatan_id', $selectedJabatanId);
            }
        }

        // Filter berdasarkan unit kerja (only for admin, sub_admin is already filtered)
        if ($request->filled('unit_kerja_id') && !$this->getUnitKerjaFilter()) {
            if ($selectedUnitKerjaId) {
                $query->where('unit_kerja_id', $selectedUnitKerjaId);
            }
        }

        // Filter berdasarkan agama
        if ($request->filled('agama')) {
            $query->where('agama', $request->agama);
        }

        $pegawai = $query->orderBy('nama')->get();
        $golongan = Golongan::orderBy('nama')->get();
        $jabatan = Jabatan::orderBy('nama')->get();
        
        // Sub admin only sees their own unit kerja
        $unitKerjaFilter = $this->getUnitKerjaFilter();
        $unitKerjaInfo = $unitKerjaFilter ? UnitKerja::find($unitKerjaFilter) : null;
        
        if ($unitKerjaFilter) {
            $unitKerja = UnitKerja::where('id', $unitKerjaFilter)->get();
        } else {
            $unitKerja = UnitKerja::orderBy('nama')->get();
        }

        $selectedGolonganParam = $this->encodeFilterId($selectedGolonganId);
        $selectedJabatanParam = $this->encodeFilterId($selectedJabatanId);
        $selectedUnitKerjaParam = $this->encodeFilterId($selectedUnitKerjaId);

        return view('laporan.pegawai', compact(
            'pegawai',
            'golongan',
            'jabatan',
            'unitKerja',
            'unitKerjaInfo',
            'selectedGolonganParam',
            'selectedJabatanParam',
            'selectedUnitKerjaParam'
        ));
    }

    /**
     * Laporan berdasarkan golongan
     */
    public function golongan(Request $request)
    {
        $unitKerjaFilter = $this->getUnitKerjaFilter();
        
        $data = Golongan::withCount(['pegawai' => function ($query) use ($unitKerjaFilter) {
                $this->applyExcludedStatusFilter($query);
                if ($unitKerjaFilter) {
                    $query->where('unit_kerja_id', $unitKerjaFilter);
                }
            }])
            ->orderBy('nama')
            ->get();

        $chartData = $data->map(function ($item) {
            return [
                'label' => $item->nama,
                'value' => $item->pegawai_count
            ];
        });
        
        $unitKerja = $unitKerjaFilter ? UnitKerja::find($unitKerjaFilter) : null;

        return view('laporan.golongan', compact('data', 'chartData', 'unitKerja'));
    }

    /**
     * Laporan berdasarkan jabatan
     */
    public function jabatan(Request $request)
    {
        $unitKerjaFilter = $this->getUnitKerjaFilter();
        $selectedUnitKerja = $unitKerjaFilter;
        $reportScope = (!$unitKerjaFilter && $request->get('scope') === 'uptd-puskesmas')
            ? 'uptd-puskesmas'
            : 'all';
        $scopeLabel = $reportScope === 'uptd-puskesmas'
            ? 'Rekap UPTD Puskesmas'
            : 'Rekap Semua Unit';

        // Admin dapat memilih unit kerja tertentu dari filter.
        if (!$unitKerjaFilter && $request->filled('unit_kerja_id')) {
            $selectedUnitKerja = $this->decodeFilterId($request->unit_kerja_id);
        }
        
        $data = Pegawai::query()
            ->select([
                'jabatan.id as jabatan_id',
                'jabatan.kode',
                'jabatan.nama as jabatan_nama',
                'unit_kerja.id as unit_kerja_id',
                'unit_kerja.nama as unit_kerja_nama',
                DB::raw('COUNT(pegawai.id) as pegawai_count'),
            ])
            ->join('jabatan', 'pegawai.jabatan_id', '=', 'jabatan.id')
            ->leftJoin('unit_kerja', 'pegawai.unit_kerja_id', '=', 'unit_kerja.id')
            ->whereNotIn('pegawai.status_pegawai', self::EXCLUDED_STATUS_FOR_COUNT)
            ->when($reportScope === 'uptd-puskesmas', function ($query) {
                $query->where('unit_kerja.nama', 'like', self::UPTD_PUSKESMAS_PREFIX . '%');
            })
            ->when($selectedUnitKerja, function ($query) use ($selectedUnitKerja) {
                $query->where('pegawai.unit_kerja_id', $selectedUnitKerja);
            })
            ->groupBy('jabatan.id', 'jabatan.kode', 'jabatan.nama', 'unit_kerja.id', 'unit_kerja.nama')
            ->orderBy('jabatan.nama')
            ->orderBy('unit_kerja.nama')
            ->get();

        $chartData = $data->map(function ($item) {
            return [
                'label' => $item->jabatan_nama . ' - ' . ($item->unit_kerja_nama ?? 'Tanpa Unit Kerja'),
                'value' => $item->pegawai_count
            ];
        });
        
        $unitKerja = $selectedUnitKerja ? UnitKerja::find($selectedUnitKerja) : null;

        if ($unitKerjaFilter) {
            $unitKerjas = UnitKerja::where('id', $unitKerjaFilter)->get();
        } elseif ($reportScope === 'uptd-puskesmas') {
            $unitKerjas = UnitKerja::where('nama', 'like', self::UPTD_PUSKESMAS_PREFIX . '%')
                ->orderBy('nama')
                ->get();
        } else {
            $unitKerjas = UnitKerja::orderBy('nama')->get();
        }

        $selectedUnitKerjaParam = $this->encodeFilterId($selectedUnitKerja);

        return view('laporan.jabatan', compact('data', 'chartData', 'unitKerja', 'unitKerjas', 'selectedUnitKerja', 'selectedUnitKerjaParam', 'reportScope', 'scopeLabel'));
    }

    /**
     * Laporan berdasarkan eselon dan status pegawai
     */
    public function eselon(Request $request)
    {
        $unitKerjaFilter = $this->getUnitKerjaFilter();
        
        // Get all unique eselon values
        $eselonList = Jabatan::whereNotNull('eselon')
            ->where('eselon', '!=', '')
            ->distinct()
            ->orderBy('eselon')
            ->pluck('eselon');
        
        // Status pegawai list
        $statusList = $this->activeStatusList();
        
        // Build cross-tabulation data
        $data = [];
        $totalPerEselon = [];
        $totalPerStatus = [];
        $grandTotal = 0;
        
        foreach ($eselonList as $eselon) {
            $data[$eselon] = [];
            $totalPerEselon[$eselon] = 0;
            
            foreach ($statusList as $status) {
                $query = Pegawai::whereHas('jabatan', function($q) use ($eselon) {
                    $q->where('eselon', $eselon);
                })->where('status_pegawai', $status);
                
                if ($unitKerjaFilter) {
                    $query->where('unit_kerja_id', $unitKerjaFilter);
                }
                
                $count = $query->count();
                $data[$eselon][$status] = $count;
                $totalPerEselon[$eselon] += $count;
                
                if (!isset($totalPerStatus[$status])) {
                    $totalPerStatus[$status] = 0;
                }
                $totalPerStatus[$status] += $count;
                $grandTotal += $count;
            }
        }
        
        // Add "Tanpa Eselon" category for pegawai without eselon
        $dataNoEselon = [];
        $totalNoEselon = 0;
        foreach ($statusList as $status) {
            $query = Pegawai::where(function($q) {
                $q->whereNull('jabatan_id')
                  ->orWhereHas('jabatan', function($jq) {
                      $jq->whereNull('eselon')->orWhere('eselon', '');
                  });
            })->where('status_pegawai', $status);
            
            if ($unitKerjaFilter) {
                $query->where('unit_kerja_id', $unitKerjaFilter);
            }
            
            $count = $query->count();
            $dataNoEselon[$status] = $count;
            $totalNoEselon += $count;
            $totalPerStatus[$status] = ($totalPerStatus[$status] ?? 0) + $count;
            $grandTotal += $count;
        }
        
        // Chart data - pegawai per eselon
        $chartDataEselon = collect($totalPerEselon)->map(function ($value, $key) {
            return ['label' => $key, 'value' => $value];
        })->values();
        
        if ($totalNoEselon > 0) {
            $chartDataEselon->push(['label' => 'Tanpa Eselon', 'value' => $totalNoEselon]);
        }
        
        // Chart data - pegawai per status
        $chartDataStatus = collect($totalPerStatus)->map(function ($value, $key) {
            return ['label' => $key, 'value' => $value];
        })->values();
        
        $unitKerja = $unitKerjaFilter ? UnitKerja::find($unitKerjaFilter) : null;

        return view('laporan.eselon', compact(
            'data', 
            'dataNoEselon',
            'eselonList', 
            'statusList', 
            'totalPerEselon', 
            'totalNoEselon',
            'totalPerStatus', 
            'grandTotal',
            'chartDataEselon',
            'chartDataStatus',
            'unitKerja'
        ));
    }

    /**
     * Laporan berdasarkan unit kerja
     */
    public function unitKerja(Request $request)
    {
        $unitKerjaFilter = $this->getUnitKerjaFilter();
        
        if ($unitKerjaFilter) {
            // Sub admin only sees their unit kerja
            $data = UnitKerja::withCount(['pegawai' => function ($query) {
                    $this->applyExcludedStatusFilter($query);
                }])
                ->where('id', $unitKerjaFilter)
                ->get();
        } else {
            $data = UnitKerja::withCount(['pegawai' => function ($query) {
                    $this->applyExcludedStatusFilter($query);
                }])
                ->orderBy('nama')
                ->get();
        }

        $chartData = $data->map(function ($item) {
            return [
                'label' => $item->nama,
                'value' => $item->pegawai_count
            ];
        });
        
        $unitKerja = $unitKerjaFilter ? UnitKerja::find($unitKerjaFilter) : null;

        return view('laporan.unit-kerja', compact('data', 'chartData', 'unitKerja'));
    }

    /**
     * Laporan pegawai akan pensiun
     */
    public function pensiun(Request $request)
    {
        $tahunPensiun = $request->input('tahun', date('Y'));
        $batasPensiun = 58; // Batas usia pensiun
        $unitKerjaFilter = $this->getUnitKerjaFilter();

        // Hitung pegawai yang akan pensiun dalam tahun tertentu
        $query = Pegawai::with(['golongan', 'jabatan', 'unitKerja'])
            ->whereNotNull('tanggal_lahir');
        $this->applyExcludedStatusFilter($query);
        
        if ($unitKerjaFilter) {
            $query->where('unit_kerja_id', $unitKerjaFilter);
        }
        
        $pegawai = $query->get()
            ->filter(function ($item) use ($tahunPensiun, $batasPensiun) {
                if (!$item->tanggal_lahir) return false;
                
                $tahunLahir = Carbon::parse($item->tanggal_lahir)->year;
                $tahunPensiunPegawai = $tahunLahir + $batasPensiun;
                
                return $tahunPensiunPegawai == $tahunPensiun;
            })
            ->sortBy('tanggal_lahir');

        // Data untuk dropdown tahun
        $tahunOptions = range(date('Y'), date('Y') + 10);
        $unitKerja = $unitKerjaFilter ? UnitKerja::find($unitKerjaFilter) : null;

        return view('laporan.pensiun', compact('pegawai', 'tahunPensiun', 'tahunOptions', 'batasPensiun', 'unitKerja'));
    }

    /**
     * Laporan berdasarkan usia
     */
    public function usia(Request $request)
    {
        $unitKerjaFilter = $this->getUnitKerjaFilter();
        $query = Pegawai::whereNotNull('tanggal_lahir');
        $this->applyExcludedStatusFilter($query);
        
        if ($unitKerjaFilter) {
            $query->where('unit_kerja_id', $unitKerjaFilter);
        }
        
        $pegawai = $query->get();

        $kelompokUsia = [
            '< 25 tahun' => 0,
            '25-30 tahun' => 0,
            '31-35 tahun' => 0,
            '36-40 tahun' => 0,
            '41-45 tahun' => 0,
            '46-50 tahun' => 0,
            '51-55 tahun' => 0,
            '> 55 tahun' => 0,
        ];

        foreach ($pegawai as $p) {
            $usia = Carbon::parse($p->tanggal_lahir)->age;
            
            if ($usia < 25) {
                $kelompokUsia['< 25 tahun']++;
            } elseif ($usia <= 30) {
                $kelompokUsia['25-30 tahun']++;
            } elseif ($usia <= 35) {
                $kelompokUsia['31-35 tahun']++;
            } elseif ($usia <= 40) {
                $kelompokUsia['36-40 tahun']++;
            } elseif ($usia <= 45) {
                $kelompokUsia['41-45 tahun']++;
            } elseif ($usia <= 50) {
                $kelompokUsia['46-50 tahun']++;
            } elseif ($usia <= 55) {
                $kelompokUsia['51-55 tahun']++;
            } else {
                $kelompokUsia['> 55 tahun']++;
            }
        }

        $chartData = collect($kelompokUsia)->map(function ($value, $key) {
            return ['label' => $key, 'value' => $value];
        })->values();

        $unitKerja = $unitKerjaFilter ? UnitKerja::find($unitKerjaFilter) : null;

        return view('laporan.usia', compact('kelompokUsia', 'chartData', 'unitKerja'));
    }

    /**
     * Laporan berdasarkan pendidikan
     */
    public function pendidikan(Request $request)
    {
        $unitKerjaFilter = $this->getUnitKerjaFilter();
        
        $query = Pegawai::select('pendidikan_terakhir', DB::raw('count(*) as total'))
            ->whereNotNull('pendidikan_terakhir')
            ->where('pendidikan_terakhir', '!=', '');
        $this->applyExcludedStatusFilter($query);
        
        if ($unitKerjaFilter) {
            $query->where('unit_kerja_id', $unitKerjaFilter);
        }
        
        $data = $query->groupBy('pendidikan_terakhir')
            ->orderByDesc('total')
            ->get();

        $chartData = $data->map(function ($item) {
            return [
                'label' => $item->pendidikan_terakhir,
                'value' => $item->total
            ];
        });

        $unitKerja = $unitKerjaFilter ? UnitKerja::find($unitKerjaFilter) : null;

        return view('laporan.pendidikan', compact('data', 'chartData', 'unitKerja'));
    }

    /**
     * Laporan berdasarkan agama
     */
    public function agama(Request $request)
    {
        $unitKerjaFilter = $this->getUnitKerjaFilter();
        
        $query = Pegawai::select('agama', DB::raw('count(*) as total'))
            ->whereNotNull('agama')
            ->where('agama', '!=', '');
        $this->applyExcludedStatusFilter($query);
        
        if ($unitKerjaFilter) {
            $query->where('unit_kerja_id', $unitKerjaFilter);
        }
        
        $data = $query->groupBy('agama')
            ->orderByDesc('total')
            ->get();

        $chartData = $data->map(function ($item) {
            return [
                'label' => $item->agama,
                'value' => $item->total
            ];
        });

        $unitKerja = $unitKerjaFilter ? UnitKerja::find($unitKerjaFilter) : null;

        return view('laporan.agama', compact('data', 'chartData', 'unitKerja'));
    }

    /**
     * Laporan statistik keseluruhan
     */
    public function statistik(Request $request)
    {
        $unitKerjaFilter = $this->getUnitKerjaFilter();
        
        // Base query builder untuk filtering
        $baseQuery = function() use ($unitKerjaFilter) {
            $query = Pegawai::query();
            $this->applyExcludedStatusFilter($query);
            if ($unitKerjaFilter) {
                $query->where('unit_kerja_id', $unitKerjaFilter);
            }
            return $query;
        };
        
        // Statistik umum
        $stats = [
            'total_pegawai' => $baseQuery()->count(),
            'pns' => $baseQuery()->where('status_pegawai', 'PNS')->count(),
            'cpns' => $baseQuery()->where('status_pegawai', 'CPNS')->count(),
            'pppk' => $baseQuery()->where('status_pegawai', 'PPPK')->count(),
            'pppk_paruh_waktu' => $baseQuery()->where('status_pegawai', 'PPPK Paruh Waktu')->count(),
            'non_asn' => $baseQuery()->where('status_pegawai', 'Non ASN')->count(),
            'berhenti_keluar' => 0,
            'pensiun' => 0,
            'laki_laki' => $baseQuery()->where('jenis_kelamin', 'L')->count(),
            'perempuan' => $baseQuery()->where('jenis_kelamin', 'P')->count(),
        ];

        // Data jenis kelamin
        $genderData = [
            ['label' => 'Laki-laki', 'value' => $stats['laki_laki']],
            ['label' => 'Perempuan', 'value' => $stats['perempuan']],
        ];

        // Data status pegawai
        $statusData = [
            ['label' => 'PNS', 'value' => $stats['pns']],
            ['label' => 'CPNS', 'value' => $stats['cpns']],
            ['label' => 'PPPK', 'value' => $stats['pppk']],
            ['label' => 'PPPK Paruh Waktu', 'value' => $stats['pppk_paruh_waktu']],
            ['label' => 'Non ASN', 'value' => $stats['non_asn']],
        ];

        // Top 10 golongan
        $topGolongan = Golongan::withCount(['pegawai' => function($query) use ($unitKerjaFilter) {
                $this->applyExcludedStatusFilter($query);
                if ($unitKerjaFilter) {
                    $query->where('unit_kerja_id', $unitKerjaFilter);
                }
            }])
            ->orderByDesc('pegawai_count')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return ['label' => $item->nama, 'value' => $item->pegawai_count];
            });

        // Top 10 jabatan
        $topJabatan = Jabatan::withCount(['pegawai' => function($query) use ($unitKerjaFilter) {
                $this->applyExcludedStatusFilter($query);
                if ($unitKerjaFilter) {
                    $query->where('unit_kerja_id', $unitKerjaFilter);
                }
            }])
            ->orderByDesc('pegawai_count')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return ['label' => $item->nama, 'value' => $item->pegawai_count];
            });

        $unitKerja = $unitKerjaFilter ? UnitKerja::find($unitKerjaFilter) : null;

        return view('laporan.statistik', compact('stats', 'genderData', 'statusData', 'topGolongan', 'topJabatan', 'unitKerja'));
    }

    /**
     * Export laporan ke Excel
     */
    public function exportExcel(Request $request)
    {
        $type = $request->input('type', 'pegawai');
        
        $filename = 'laporan_' . $type . '_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($type, $request) {
            $file = fopen('php://output', 'w');
            
            // BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            switch ($type) {
                case 'pegawai':
                    $this->exportPegawaiCsv($file, $request);
                    break;
                case 'golongan':
                    $this->exportGolonganCsv($file);
                    break;
                case 'jabatan':
                    $this->exportJabatanCsv($file, $request);
                    break;
                case 'eselon':
                    $this->exportEselonCsv($file);
                    break;
                case 'unit_kerja':
                    $this->exportUnitKerjaCsv($file);
                    break;
                case 'pensiun':
                    $this->exportPensiunCsv($file, $request);
                    break;
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Force Excel to treat CSV value as text to preserve leading zeros.
     */
    private function asExcelText($value)
    {
        if ($value === null || $value === '') {
            return '';
        }

        $safe = str_replace('"', '""', (string) $value);
        return '="' . $safe . '"';
    }

    private function exportPegawaiCsv($file, $request)
    {
        // Header
        fputcsv($file, [
            'No', 'NIP', 'NIK', 'Nama', 'Tempat Lahir', 'Tanggal Lahir', 
            'Jenis Kelamin', 'Agama', 'Status Pegawai', 'Golongan', 
            'Jabatan', 'Unit Kerja', 'Pendidikan', 'Email', 'Telepon', 'Alamat'
        ]);

        $query = Pegawai::with(['golongan', 'jabatan', 'unitKerja']);
        $golonganId = $this->decodeFilterId($request->get('golongan_id'));
        $jabatanId = $this->decodeFilterId($request->get('jabatan_id'));
        $unitKerjaId = $this->decodeFilterId($request->get('unit_kerja_id'));
        
        if ($request->filled('status_pegawai')) {
            $query->where('status_pegawai', $request->status_pegawai);
        }
        if ($request->filled('golongan_id')) {
            if ($golonganId) {
                $query->where('golongan_id', $golonganId);
            }
        }
        if ($request->filled('jabatan_id')) {
            if ($jabatanId) {
                $query->where('jabatan_id', $jabatanId);
            }
        }
        if ($request->filled('unit_kerja_id')) {
            if ($unitKerjaId) {
                $query->where('unit_kerja_id', $unitKerjaId);
            }
        }

        $pegawai = $query->orderBy('nama')->get();
        
        $no = 1;
        foreach ($pegawai as $p) {
            fputcsv($file, [
                $no++,
                $this->asExcelText($p->nip),
                $this->asExcelText($p->nik),
                $p->nama,
                $p->tempat_lahir,
                $p->tanggal_lahir ? date('d/m/Y', strtotime($p->tanggal_lahir)) : '',
                $p->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
                $p->agama,
                $p->status_pegawai,
                $p->golongan?->nama ?? '-',
                $p->jabatan?->nama ?? '-',
                $p->unitKerja?->nama ?? '-',
                $p->pendidikan_terakhir,
                $p->email,
                $this->asExcelText($p->telepon),
                $p->alamat,
            ]);
        }
    }

    private function exportGolonganCsv($file)
    {
        fputcsv($file, ['No', 'Kode', 'Nama Golongan', 'Jumlah Pegawai']);
        
        $data = Golongan::withCount(['pegawai' => function ($query) {
            $this->applyExcludedStatusFilter($query);
            }])->orderBy('nama')->get();
        
        $no = 1;
        foreach ($data as $item) {
            fputcsv($file, [$no++, $item->kode, $item->nama, $item->pegawai_count]);
        }
    }

    private function exportJabatanCsv($file, $request)
    {
        fputcsv($file, ['No', 'Kode', 'Nama Jabatan', 'Unit Kerja', 'Jumlah Pegawai']);

        $unitKerjaFilter = $this->getUnitKerjaFilter();
        $selectedUnitKerja = $unitKerjaFilter;
        $reportScope = (!$unitKerjaFilter && $request->get('scope') === 'uptd-puskesmas')
            ? 'uptd-puskesmas'
            : 'all';
        if (!$unitKerjaFilter && $request->filled('unit_kerja_id')) {
            $selectedUnitKerja = $this->decodeFilterId($request->unit_kerja_id);
        }

        $data = Pegawai::query()
            ->select([
                'jabatan.kode',
                'jabatan.nama as jabatan_nama',
                'unit_kerja.nama as unit_kerja_nama',
                DB::raw('COUNT(pegawai.id) as pegawai_count'),
            ])
            ->join('jabatan', 'pegawai.jabatan_id', '=', 'jabatan.id')
            ->leftJoin('unit_kerja', 'pegawai.unit_kerja_id', '=', 'unit_kerja.id')
            ->whereNotIn('pegawai.status_pegawai', self::EXCLUDED_STATUS_FOR_COUNT)
            ->when($reportScope === 'uptd-puskesmas', function ($query) {
                $query->where('unit_kerja.nama', 'like', self::UPTD_PUSKESMAS_PREFIX . '%');
            })
            ->when($selectedUnitKerja, function ($query) use ($selectedUnitKerja) {
                $query->where('pegawai.unit_kerja_id', $selectedUnitKerja);
            })
            ->groupBy('jabatan.kode', 'jabatan.nama', 'unit_kerja.nama')
            ->orderBy('jabatan.nama')
            ->orderBy('unit_kerja.nama')
            ->get();
        
        $no = 1;
        foreach ($data as $item) {
            fputcsv($file, [
                $no++,
                $item->kode,
                $item->jabatan_nama,
                $item->unit_kerja_nama ?? 'Tanpa Unit Kerja',
                $item->pegawai_count,
            ]);
        }
    }

    private function exportUnitKerjaCsv($file)
    {
        fputcsv($file, ['No', 'Kode', 'Nama Unit Kerja', 'Jumlah Pegawai']);
        
        $data = UnitKerja::withCount(['pegawai' => function ($query) {
            $this->applyExcludedStatusFilter($query);
            }])->orderBy('nama')->get();
        
        $no = 1;
        foreach ($data as $item) {
            fputcsv($file, [$no++, $item->kode, $item->nama, $item->pegawai_count]);
        }
    }

    private function exportEselonCsv($file)
    {
        $unitKerjaFilter = $this->getUnitKerjaFilter();
        
        // Get all unique eselon values
        $eselonList = Jabatan::whereNotNull('eselon')
            ->where('eselon', '!=', '')
            ->distinct()
            ->orderBy('eselon')
            ->pluck('eselon');
        
        $statusList = $this->activeStatusList();
        
        // Header
        $header = ['No', 'Eselon'];
        foreach ($statusList as $status) {
            $header[] = $status;
        }
        $header[] = 'Total';
        fputcsv($file, $header);
        
        $no = 1;
        $totalPerStatus = array_fill_keys($statusList, 0);
        $grandTotal = 0;
        
        foreach ($eselonList as $eselon) {
            $row = [$no++, $eselon];
            $totalEselon = 0;
            
            foreach ($statusList as $status) {
                $query = Pegawai::whereHas('jabatan', function($q) use ($eselon) {
                    $q->where('eselon', $eselon);
                })->where('status_pegawai', $status);
                
                if ($unitKerjaFilter) {
                    $query->where('unit_kerja_id', $unitKerjaFilter);
                }
                
                $count = $query->count();
                $row[] = $count;
                $totalEselon += $count;
                $totalPerStatus[$status] += $count;
            }
            
            $row[] = $totalEselon;
            $grandTotal += $totalEselon;
            fputcsv($file, $row);
        }
        
        // Tanpa Eselon
        $row = [$no++, 'Tanpa Eselon'];
        $totalNoEselon = 0;
        foreach ($statusList as $status) {
            $query = Pegawai::where(function($q) {
                $q->whereNull('jabatan_id')
                  ->orWhereHas('jabatan', function($jq) {
                      $jq->whereNull('eselon')->orWhere('eselon', '');
                  });
            })->where('status_pegawai', $status);
            
            if ($unitKerjaFilter) {
                $query->where('unit_kerja_id', $unitKerjaFilter);
            }
            
            $count = $query->count();
            $row[] = $count;
            $totalNoEselon += $count;
            $totalPerStatus[$status] += $count;
        }
        $row[] = $totalNoEselon;
        $grandTotal += $totalNoEselon;
        fputcsv($file, $row);
        
        // Total row
        $totalRow = ['', 'Total'];
        foreach ($statusList as $status) {
            $totalRow[] = $totalPerStatus[$status];
        }
        $totalRow[] = $grandTotal;
        fputcsv($file, $totalRow);
    }

    private function exportPensiunCsv($file, $request)
    {
        $tahunPensiun = $request->input('tahun', date('Y'));
        $batasPensiun = 58;

        fputcsv($file, ['No', 'NIP', 'Nama', 'Tanggal Lahir', 'Usia', 'Golongan', 'Jabatan', 'Unit Kerja', 'Tahun Pensiun']);
        
        $pegawai = Pegawai::with(['golongan', 'jabatan', 'unitKerja'])
            ->whereNotNull('tanggal_lahir')
            ->whereNotIn('status_pegawai', self::EXCLUDED_STATUS_FOR_COUNT)
            ->get()
            ->filter(function ($item) use ($tahunPensiun, $batasPensiun) {
                if (!$item->tanggal_lahir) return false;
                $tahunLahir = Carbon::parse($item->tanggal_lahir)->year;
                return ($tahunLahir + $batasPensiun) == $tahunPensiun;
            })
            ->sortBy('tanggal_lahir');
        
        $no = 1;
        foreach ($pegawai as $p) {
            $usia = Carbon::parse($p->tanggal_lahir)->age;
            fputcsv($file, [
                $no++,
                $this->asExcelText($p->nip),
                $p->nama,
                date('d/m/Y', strtotime($p->tanggal_lahir)),
                $usia . ' tahun',
                $p->golongan?->nama ?? '-',
                $p->jabatan?->nama ?? '-',
                $p->unitKerja?->nama ?? '-',
                $tahunPensiun,
            ]);
        }
    }

    /**
     * Print laporan
     */
    public function print(Request $request)
    {
        $type = $request->input('type', 'pegawai');
        
        switch ($type) {
            case 'pegawai':
                return $this->printPegawai($request);
            case 'golongan':
                return $this->printGolongan($request);
            case 'jabatan':
                return $this->printJabatan($request);
            case 'eselon':
                return $this->printEselon($request);
            case 'unit_kerja':
                return $this->printUnitKerja($request);
            case 'pensiun':
                return $this->printPensiun($request);
            case 'statistik':
                return $this->printStatistik($request);
            default:
                return redirect()->back();
        }
    }

    private function printPegawai($request)
    {
        $query = Pegawai::with(['golongan', 'jabatan', 'unitKerja']);
        $golonganId = $this->decodeFilterId($request->get('golongan_id'));
        $jabatanId = $this->decodeFilterId($request->get('jabatan_id'));
        $unitKerjaId = $this->decodeFilterId($request->get('unit_kerja_id'));
        
        if ($request->filled('status_pegawai')) {
            $query->where('status_pegawai', $request->status_pegawai);
        }
        if ($request->filled('golongan_id')) {
            if ($golonganId) {
                $query->where('golongan_id', $golonganId);
            }
        }
        if ($request->filled('jabatan_id')) {
            if ($jabatanId) {
                $query->where('jabatan_id', $jabatanId);
            }
        }
        if ($request->filled('unit_kerja_id')) {
            if ($unitKerjaId) {
                $query->where('unit_kerja_id', $unitKerjaId);
            }
        }

        $data = $query->orderBy('nama')->get();
        $title = 'Laporan Data Pegawai';
        $filters = [
            'status_pegawai' => $request->input('status_pegawai'),
            'golongan_id' => $golonganId,
            'jabatan_id' => $jabatanId,
            'unit_kerja_id' => $unitKerjaId,
        ];

        return view('laporan.print.pegawai', compact('data', 'title', 'filters'));
    }

    private function printGolongan($request)
    {
        $data = Golongan::select('golongan.id', 'golongan.nama as golongan_nama')
            ->withCount(['pegawai as total' => function ($query) {
                $this->applyExcludedStatusFilter($query);
            }])
            ->orderBy('golongan.nama')
            ->get();
        $title = 'Laporan Data Pegawai Berdasarkan Golongan';

        return view('laporan.print.golongan', compact('data', 'title'));
    }

    private function printJabatan($request)
    {
        $unitKerjaFilter = $this->getUnitKerjaFilter();
        $selectedUnitKerja = $unitKerjaFilter;
        $reportScope = (!$unitKerjaFilter && $request->get('scope') === 'uptd-puskesmas')
            ? 'uptd-puskesmas'
            : 'all';
        if (!$unitKerjaFilter && $request->filled('unit_kerja_id')) {
            $selectedUnitKerja = $this->decodeFilterId($request->unit_kerja_id);
        }

        $data = Pegawai::query()
            ->select([
                'jabatan.id as jabatan_id',
                'jabatan.nama as jabatan_nama',
                'unit_kerja.nama as unit_kerja_nama',
                DB::raw('COUNT(pegawai.id) as total'),
            ])
            ->join('jabatan', 'pegawai.jabatan_id', '=', 'jabatan.id')
            ->leftJoin('unit_kerja', 'pegawai.unit_kerja_id', '=', 'unit_kerja.id')
            ->whereNotIn('pegawai.status_pegawai', self::EXCLUDED_STATUS_FOR_COUNT)
            ->when($reportScope === 'uptd-puskesmas', function ($query) {
                $query->where('unit_kerja.nama', 'like', self::UPTD_PUSKESMAS_PREFIX . '%');
            })
            ->when($selectedUnitKerja, function ($query) use ($selectedUnitKerja) {
                $query->where('pegawai.unit_kerja_id', $selectedUnitKerja);
            })
            ->groupBy('jabatan.id', 'jabatan.nama', 'unit_kerja.nama')
            ->orderBy('jabatan.nama')
            ->orderBy('unit_kerja.nama')
            ->get();
        $title = 'Laporan Data Pegawai Berdasarkan Jabatan';
        $unitKerja = $selectedUnitKerja ? UnitKerja::find($selectedUnitKerja) : null;
        $scopeLabel = $reportScope === 'uptd-puskesmas' ? 'UPTD Puskesmas' : 'Semua Unit';

        return view('laporan.print.jabatan', compact('data', 'title', 'unitKerja', 'scopeLabel'));
    }

    private function printEselon($request)
    {
        $unitKerjaFilter = $this->getUnitKerjaFilter();
        
        $eselonList = Jabatan::whereNotNull('eselon')
            ->where('eselon', '!=', '')
            ->distinct()
            ->orderBy('eselon')
            ->pluck('eselon');
        
        $statusList = $this->activeStatusList();
        
        $data = [];
        $totalPerEselon = [];
        $totalPerStatus = [];
        $grandTotal = 0;
        
        foreach ($eselonList as $eselon) {
            $data[$eselon] = [];
            $totalPerEselon[$eselon] = 0;
            
            foreach ($statusList as $status) {
                $query = Pegawai::whereHas('jabatan', function($q) use ($eselon) {
                    $q->where('eselon', $eselon);
                })->where('status_pegawai', $status);
                
                if ($unitKerjaFilter) {
                    $query->where('unit_kerja_id', $unitKerjaFilter);
                }
                
                $count = $query->count();
                $data[$eselon][$status] = $count;
                $totalPerEselon[$eselon] += $count;
                
                if (!isset($totalPerStatus[$status])) {
                    $totalPerStatus[$status] = 0;
                }
                $totalPerStatus[$status] += $count;
                $grandTotal += $count;
            }
        }
        
        // Tanpa Eselon
        $dataNoEselon = [];
        $totalNoEselon = 0;
        foreach ($statusList as $status) {
            $query = Pegawai::where(function($q) {
                $q->whereNull('jabatan_id')
                  ->orWhereHas('jabatan', function($jq) {
                      $jq->whereNull('eselon')->orWhere('eselon', '');
                  });
            })->where('status_pegawai', $status);
            
            if ($unitKerjaFilter) {
                $query->where('unit_kerja_id', $unitKerjaFilter);
            }
            
            $count = $query->count();
            $dataNoEselon[$status] = $count;
            $totalNoEselon += $count;
            $totalPerStatus[$status] = ($totalPerStatus[$status] ?? 0) + $count;
            $grandTotal += $count;
        }
        
        $title = 'Laporan Data Pegawai Berdasarkan Eselon dan Status';
        $unitKerja = $unitKerjaFilter ? UnitKerja::find($unitKerjaFilter) : null;

        return view('laporan.print.eselon', compact(
            'data', 
            'dataNoEselon',
            'eselonList', 
            'statusList', 
            'totalPerEselon', 
            'totalNoEselon',
            'totalPerStatus', 
            'grandTotal',
            'title',
            'unitKerja'
        ));
    }

    private function printUnitKerja($request)
    {
        $data = UnitKerja::select('unit_kerja.id', 'unit_kerja.nama as unit_kerja_nama')
            ->withCount(['pegawai as total' => function ($query) {
                $this->applyExcludedStatusFilter($query);
            }])
            ->orderBy('unit_kerja.nama')
            ->get();
        $title = 'Laporan Data Pegawai Berdasarkan Unit Kerja';

        return view('laporan.print.unit-kerja', compact('data', 'title'));
    }

    private function printPensiun($request)
    {
        $tahunPensiun = $request->input('tahun', date('Y'));
        $batasPensiun = 58;

        $data = Pegawai::with(['golongan', 'jabatan', 'unitKerja'])
            ->whereNotNull('tanggal_lahir')
            ->whereNotIn('status_pegawai', self::EXCLUDED_STATUS_FOR_COUNT)
            ->get()
            ->filter(function ($item) use ($tahunPensiun, $batasPensiun) {
                if (!$item->tanggal_lahir) return false;
                $tahunLahir = Carbon::parse($item->tanggal_lahir)->year;
                return ($tahunLahir + $batasPensiun) == $tahunPensiun;
            })
            ->sortBy('tanggal_lahir');

        $title = 'Laporan Pegawai Akan Pensiun Tahun ' . $tahunPensiun;
        $year = $tahunPensiun;

        return view('laporan.print.pensiun', compact('data', 'title', 'year', 'batasPensiun'));
    }

    private function printStatistik($request)
    {
        $baseQuery = Pegawai::query()->whereNotIn('status_pegawai', self::EXCLUDED_STATUS_FOR_COUNT);

        $stats = [
            'total_pegawai' => (clone $baseQuery)->count(),
            'pns' => (clone $baseQuery)->where('status_pegawai', 'PNS')->count(),
            'cpns' => (clone $baseQuery)->where('status_pegawai', 'CPNS')->count(),
            'pppk' => (clone $baseQuery)->where('status_pegawai', 'PPPK')->count(),
            'pppk_paruh_waktu' => (clone $baseQuery)->where('status_pegawai', 'PPPK Paruh Waktu')->count(),
            'non_asn' => (clone $baseQuery)->where('status_pegawai', 'Non ASN')->count(),
            'berhenti_keluar' => 0,
            'pensiun' => 0,
            'laki_laki' => (clone $baseQuery)->where('jenis_kelamin', 'L')->count(),
            'perempuan' => (clone $baseQuery)->where('jenis_kelamin', 'P')->count(),
        ];

        $topGolongan = Golongan::withCount(['pegawai' => function ($query) {
                $this->applyExcludedStatusFilter($query);
            }])
            ->orderByDesc('pegawai_count')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return ['label' => $item->nama, 'value' => $item->pegawai_count];
            })->toArray();
            
        $topJabatan = Jabatan::withCount(['pegawai' => function ($query) {
                $this->applyExcludedStatusFilter($query);
            }])
            ->orderByDesc('pegawai_count')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return ['label' => $item->nama, 'value' => $item->pegawai_count];
            })->toArray();
        
        $title = 'Laporan Statistik Kepegawaian';

        return view('laporan.print.statistik', compact('stats', 'topGolongan', 'topJabatan', 'title'));
    }
}
