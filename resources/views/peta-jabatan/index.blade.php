@extends('layouts.app')

@section('title', 'Peta Jabatan')
@section('page-title', 'Peta Jabatan per Unit Kerja')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-diagram-3 me-2"></i>Peta Jabatan</span>
                <div>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('peta-jabatan.rekap') }}" class="btn btn-info btn-sm me-1">
                        <i class="bi bi-bar-chart me-1"></i>Rekap Semua Unit
                    </a>
                    <a href="{{ route('peta-jabatan.rekap-uptd-puskesmas') }}" class="btn btn-outline-info btn-sm me-1">
                        <i class="bi bi-hospital me-1"></i>Rekap Unit Kerja UPTD Puskesmas
                    </a>
                    @endif
                    @if(empty($isAggregatedRecap))
                    <a href="{{ route('peta-jabatan.set-kebutuhan', ['unit_kerja_id' => $selectedUnitKerjaParam]) }}" class="btn btn-success btn-sm">
                        <i class="bi bi-gear me-1"></i>Atur Kebutuhan
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Unit Kerja -->
                <form method="GET" action="{{ route('peta-jabatan.index') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Pilih Unit Kerja</label>
                            <select name="unit_kerja_id" class="form-select select2" onchange="this.form.submit()">
                                <option value="">-- Pilih Unit Kerja --</option>
                                @if(auth()->user()->isAdmin())
                                <option value="{{ $uptdPuskesmasScopeValue }}" {{ (string) $selectedUnitKerja === (string) $uptdPuskesmasScopeValue ? 'selected' : '' }}>
                                    Rekap UPTD Puskesmas (Semua Unit)
                                </option>
                                <option value="{{ $dinasKesehatanScopeValue }}" {{ (string) $selectedUnitKerja === (string) $dinasKesehatanScopeValue ? 'selected' : '' }}>
                                    Rekap Dinas Kesehatan (ID 1-6)
                                </option>
                                @endif
                                @foreach($unitKerjas as $uk)
                                <option value="{{ \App\Helpers\IdEncoder::encode($uk->id) }}" {{ $selectedUnitKerja == $uk->id ? 'selected' : '' }}>
                                    {{ $uk->nama }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @if($selectedUnitKerja && $petaJabatan->count() > 0)
                        <div class="col-md-6 text-md-end align-self-end mt-3 mt-md-0">
                            <a href="{{ route('peta-jabatan.export', ['unit_kerja_id' => $selectedUnitKerjaParam]) }}" class="btn btn-success btn-sm me-1">
                                <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
                            </a>
                            <a href="{{ route('peta-jabatan.print', ['unit_kerja_id' => $selectedUnitKerjaParam]) }}" class="btn btn-secondary btn-sm" target="_blank">
                                <i class="bi bi-printer me-1"></i>Cetak
                            </a>
                        </div>
                        @endif
                    </div>
                </form>

                @if(!empty($isAggregatedRecap))
                    <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                        <i class="bi bi-info-circle me-2"></i>
                        {{ $recapInfoMessage }}
                    </div>
                @endif

                @if($showPetaJabatan)
                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="stat-card bg-primary position-relative">
                                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                                <h6 class="text-white-50 mb-1">Total Bezetting</h6>
                                <h2 class="mb-0">{{ $totalBezetting }}</h2>
                                <small>{{ !empty($isAggregatedRecap) ? ('Akumulasi ' . ($selectedUnitKerjaLabel ?? 'Rekap')) : 'Pegawai Eksisting' }}</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card bg-info position-relative">
                                <div class="stat-icon"><i class="bi bi-clipboard-check"></i></div>
                                <h6 class="text-white-50 mb-1">Total Kebutuhan</h6>
                                <h2 class="mb-0">{{ $totalKebutuhan }}</h2>
                                <small>{{ !empty($isAggregatedRecap) ? ('Akumulasi ' . ($selectedUnitKerjaLabel ?? 'Rekap')) : 'Pegawai Dibutuhkan' }}</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            @php
                                $totalSelisih = $totalBezetting - $totalKebutuhan;
                                $statusClass = $totalSelisih == 0 ? 'success' : ($totalSelisih > 0 ? 'warning' : 'danger');
                            @endphp
                            <div class="stat-card bg-{{ $statusClass }} position-relative">
                                <div class="stat-icon"><i class="bi bi-graph-{{ $totalSelisih >= 0 ? 'up' : 'down' }}"></i></div>
                                <h6 class="text-white-50 mb-1">Selisih</h6>
                                <h2 class="mb-0">{{ $totalSelisih >= 0 ? '+' : '' }}{{ $totalSelisih }}</h2>
                                <small>{{ $totalSelisih == 0 ? 'Terpenuhi' : ($totalSelisih > 0 ? 'Kelebihan' : 'Kekurangan') }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Peta Jabatan Chart -->
                    <div class="row mb-4">
                        <div class="col-lg-8 mb-3 mb-lg-0">
                            <div class="card h-100">
                                <div class="card-header">
                                    <i class="bi bi-bar-chart-line me-2"></i>Perbandingan Bezetting vs Kebutuhan
                                </div>
                                <div class="card-body">
                                    <div style="height: 420px;">
                                        <canvas id="petaJabatanComparisonChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <i class="bi bi-pie-chart me-2"></i>Komposisi Status Jabatan
                                </div>
                                <div class="card-body d-flex align-items-center justify-content-center">
                                    <div style="width: 100%; max-width: 320px;">
                                        <canvas id="petaJabatanStatusChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Struktur Organisasi Tree -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <span><i class="bi bi-diagram-3-fill me-2"></i>Diagram Struktur Organisasi (Tree)</span>
                            <div class="d-flex align-items-center gap-2">
                                <div class="btn-group btn-group-sm org-mode-toggle" role="group" aria-label="Mode Tampilan Org Chart">
                                    <button type="button" class="btn btn-outline-primary active" id="orgModeCompactBtn">
                                        <i class="bi bi-layout-three-columns me-1"></i>Horizontal Compact
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="orgModeDetailBtn">
                                        <i class="bi bi-view-stacked me-1"></i>Vertikal Detail
                                    </button>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="orgModeResetBtn" title="Reset ke mode default unit kerja ini">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">
                                Diagram ini menyajikan struktur jabatan per unit kerja berdasarkan level eselon. Pilih mode tampilan sesuai kebutuhan analisis.
                            </p>
                            <div id="orgModeCompactPanel" class="org-mode-panel">
                                <div class="org-tree-wrap">
                                    <div id="orgTreeCompactContainer"></div>
                                </div>
                            </div>
                            <div id="orgModeDetailPanel" class="org-mode-panel d-none">
                                <div id="orgTreeDetailContainer"></div>
                            </div>
                        </div>
                    </div>

                    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1080;">
                        <div id="orgModeToast" class="toast align-items-center text-bg-success border-0" role="status" aria-live="polite" aria-atomic="true">
                            <div class="d-flex">
                                <div class="toast-body" id="orgModeToastBody">Mode tampilan sudah diperbarui.</div>
                                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                        </div>
                    </div>

                    <!-- Peta Jabatan Table -->
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="50" class="text-center">No</th>
                                    <th>Kode</th>
                                    <th>Nama Jabatan</th>
                                    <th class="text-center" width="100">Bezetting</th>
                                    <th class="text-center" width="100">Kebutuhan</th>
                                    <th class="text-center" width="100">Selisih</th>
                                    <th class="text-center" width="120">Status</th>
                                    <th>Keterangan</th>
                                    <th class="text-center" width="80">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($petaJabatan as $i => $item)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td><strong>{{ $item['jabatan']->kode }}</strong></td>
                                    <td>{{ $item['jabatan']->nama }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary fs-6">{{ $item['bezetting'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info fs-6">{{ $item['kebutuhan'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $item['status_class'] }} fs-6">
                                            {{ $item['selisih'] >= 0 ? '+' : '' }}{{ $item['selisih'] }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $item['status_class'] }}">{{ $item['status'] }}</span>
                                    </td>
                                    <td>{{ $item['keterangan'] ?? '-' }}</td>
                                    <td class="text-center">
                                        @if(empty($isAggregatedRecap) && $item['bezetting'] > 0)
                                        <a href="{{ route('peta-jabatan.detail-pegawai', ['unit_kerja_id' => $selectedUnitKerjaParam, 'jabatan_id' => $item['jabatan']->getRouteKey()]) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Lihat Pegawai">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        Belum ada data peta jabatan untuk pilihan ini.
                                        @if(empty($isAggregatedRecap))
                                        <br>
                                        <a href="{{ route('peta-jabatan.set-kebutuhan', ['unit_kerja_id' => $selectedUnitKerjaParam]) }}">
                                            Klik di sini untuk mengatur kebutuhan pegawai.
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if($petaJabatan->count() > 0)
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th class="text-center">{{ $totalBezetting }}</th>
                                    <th class="text-center">{{ $totalKebutuhan }}</th>
                                    <th class="text-center">{{ $totalBezetting - $totalKebutuhan >= 0 ? '+' : '' }}{{ $totalBezetting - $totalKebutuhan }}</th>
                                    <th colspan="3"></th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-diagram-3 fs-1 d-block mb-3"></i>
                        <h5>Pilih Unit Kerja</h5>
                        <p>Silakan pilih unit kerja untuk melihat peta jabatan.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Legend -->
@if($selectedUnitKerja && $petaJabatan->count() > 0)
<div class="card">
    <div class="card-body">
        <h6 class="card-title"><i class="bi bi-info-circle me-2"></i>Keterangan</h6>
        <div class="row">
            <div class="col-md-4">
                <span class="badge bg-success me-1">Terpenuhi</span>
                <small class="text-muted">Jumlah pegawai sesuai kebutuhan</small>
            </div>
            <div class="col-md-4">
                <span class="badge bg-warning me-1">Kelebihan</span>
                <small class="text-muted">Jumlah pegawai melebihi kebutuhan</small>
            </div>
            <div class="col-md-4">
                <span class="badge bg-danger me-1">Kekurangan</span>
                <small class="text-muted">Jumlah pegawai kurang dari kebutuhan</small>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6">
                <strong>Bezetting:</strong> Jumlah pegawai eksisting berdasarkan jabatan per unit kerja
            </div>
            <div class="col-md-6">
                <strong>Kebutuhan:</strong> Jumlah pegawai yang dibutuhkan berdasarkan jabatan per unit kerja
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@if($showPetaJabatan && $petaJabatan->count() > 0)
@push('styles')
<style>
.org-tree-wrap {
    overflow-x: auto;
    padding-bottom: 0.5rem;
}

.org-mode-toggle .btn.active {
    background-color: #0d6efd;
    color: #fff;
    border-color: #0d6efd;
}

.org-tree,
.org-tree ul {
    margin: 0;
    padding-top: 20px;
    position: relative;
    white-space: nowrap;
    text-align: center;
}

.org-tree ul::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    border-left: 1px solid #c7d1dc;
    width: 0;
    height: 20px;
}

.org-tree li {
    list-style: none;
    position: relative;
    padding: 20px 8px 0 8px;
    display: inline-block;
    vertical-align: top;
}

.org-tree li::before,
.org-tree li::after {
    content: '';
    position: absolute;
    top: 0;
    right: 50%;
    border-top: 1px solid #c7d1dc;
    width: 50%;
    height: 20px;
}

.org-tree li::after {
    right: auto;
    left: 50%;
    border-left: 1px solid #c7d1dc;
}

.org-tree li:only-child::before,
.org-tree li:only-child::after {
    display: none;
}

.org-tree li:only-child {
    padding-top: 0;
}

.org-tree li:first-child::before,
.org-tree li:last-child::after {
    border: 0;
}

.org-tree li:last-child::before {
    border-right: 1px solid #c7d1dc;
    border-radius: 0 6px 0 0;
}

.org-tree li:first-child::after {
    border-radius: 6px 0 0 0;
}

.org-node {
    min-width: 220px;
    max-width: 240px;
    background: #fff;
    border: 1px solid #dbe3eb;
    border-radius: 10px;
    padding: 10px;
    box-shadow: 0 3px 8px rgba(18, 38, 63, 0.06);
    margin: 0 auto;
}

.org-node-root {
    border-color: #3498db;
    background: linear-gradient(180deg, #ffffff 0%, #f2f8fc 100%);
}

.org-node-group {
    border-color: #17a2b8;
    background: linear-gradient(180deg, #ffffff 0%, #f2fbfc 100%);
}

.org-node-jabatan {
    border-left-width: 4px;
}

.org-node-jabatan.status-success {
    border-left-color: #27ae60;
}

.org-node-jabatan.status-warning {
    border-left-color: #f39c12;
}

.org-node-jabatan.status-danger {
    border-left-color: #e74c3c;
}

.org-node-title {
    font-weight: 600;
    color: #2c3e50;
    line-height: 1.3;
}

.org-node-subtitle {
    font-size: 0.78rem;
    color: #6c757d;
    margin-top: 2px;
}

.org-node-meta {
    margin-top: 8px;
    display: flex;
    justify-content: center;
    gap: 6px;
    flex-wrap: wrap;
}

.org-node-link {
    color: inherit;
    text-decoration: none;
}

.org-node-link:hover {
    text-decoration: underline;
}

.org-tree-compact .org-node {
    min-width: 180px;
    max-width: 195px;
    padding: 8px;
}

.org-tree-compact .org-node-title {
    font-size: 0.86rem;
}

.org-tree-compact .org-node-subtitle {
    font-size: 0.72rem;
}

.org-detail-root {
    border: 1px solid #d9e4ee;
    border-radius: 10px;
    padding: 12px;
    background: linear-gradient(180deg, #ffffff 0%, #f6f9fc 100%);
}

.org-detail-root-title {
    font-weight: 700;
    color: #2c3e50;
}

.org-detail-root-meta {
    margin-top: 8px;
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.org-detail-group {
    border: 1px solid #dbe3eb;
    border-radius: 10px;
    padding: 10px;
    margin-top: 12px;
    background-color: #fff;
}

.org-detail-group-header {
    display: flex;
    justify-content: space-between;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 8px;
}

.org-detail-group-title {
    font-weight: 600;
    color: #2c3e50;
}

.org-detail-group-meta {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.org-detail-table {
    margin-bottom: 0;
}

.org-detail-table th {
    font-size: 0.8rem;
    background: #f3f6f9;
}

.org-detail-table td {
    vertical-align: middle;
    font-size: 0.84rem;
}

@media (max-width: 768px) {
    .org-node {
        min-width: 190px;
        max-width: 200px;
    }

    .org-tree-compact .org-node {
        min-width: 165px;
        max-width: 180px;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@php
    $petaJabatanChartPayload = $petaJabatan->map(function ($item) use ($selectedUnitKerjaParam) {
        return [
            'kode' => $item['jabatan']->kode,
            'label' => $item['jabatan']->nama,
            'eselon' => $item['jabatan']->eselon,
            'bezetting' => (int) $item['bezetting'],
            'kebutuhan' => (int) $item['kebutuhan'],
            'status' => $item['status'],
            'statusClass' => $item['status_class'],
            'hasDetail' => empty($isAggregatedRecap) && (int) $item['bezetting'] > 0,
            'detailUrl' => empty($isAggregatedRecap)
                ? route('peta-jabatan.detail-pegawai', [
                    'unit_kerja_id' => $selectedUnitKerjaParam,
                    'jabatan_id' => $item['jabatan']->getRouteKey(),
                ])
                : null,
        ];
    })->values();
@endphp
<script>
const petaJabatanChartData = @json($petaJabatanChartPayload);

if (petaJabatanChartData.length > 0) {
    const labels = petaJabatanChartData.map(item => item.label.length > 42 ? item.label.substring(0, 42) + '...' : item.label);
    const bezettingData = petaJabatanChartData.map(item => item.bezetting);
    const kebutuhanData = petaJabatanChartData.map(item => item.kebutuhan);

    const comparisonCanvas = document.getElementById('petaJabatanComparisonChart');
    if (comparisonCanvas) {
        new Chart(comparisonCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Bezetting',
                        data: bezettingData,
                        backgroundColor: 'rgba(52, 152, 219, 0.75)',
                        borderColor: 'rgba(52, 152, 219, 1)',
                        borderWidth: 1,
                        borderRadius: 6,
                    },
                    {
                        label: 'Kebutuhan',
                        data: kebutuhanData,
                        backgroundColor: 'rgba(23, 162, 184, 0.75)',
                        borderColor: 'rgba(23, 162, 184, 1)',
                        borderWidth: 1,
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        ticks: {
                            maxRotation: 60,
                            minRotation: 45
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            afterLabel: function (context) {
                                const dataIndex = context.dataIndex;
                                const source = petaJabatanChartData[dataIndex];
                                return 'Status: ' + source.status;
                            }
                        }
                    }
                }
            }
        });
    }

    const statusCounter = {
        Terpenuhi: 0,
        Kelebihan: 0,
        Kekurangan: 0,
    };

    petaJabatanChartData.forEach(item => {
        if (Object.prototype.hasOwnProperty.call(statusCounter, item.status)) {
            statusCounter[item.status] += 1;
        }
    });

    const statusCanvas = document.getElementById('petaJabatanStatusChart');
    if (statusCanvas) {
        new Chart(statusCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(statusCounter),
                datasets: [{
                    data: Object.values(statusCounter),
                    backgroundColor: [
                        'rgba(39, 174, 96, 0.85)',
                        'rgba(243, 156, 18, 0.85)',
                        'rgba(231, 76, 60, 0.85)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    const orgTreeCompactContainer = document.getElementById('orgTreeCompactContainer');
    const orgTreeDetailContainer = document.getElementById('orgTreeDetailContainer');
    const orgModeCompactPanel = document.getElementById('orgModeCompactPanel');
    const orgModeDetailPanel = document.getElementById('orgModeDetailPanel');
    const orgModeCompactBtn = document.getElementById('orgModeCompactBtn');
    const orgModeDetailBtn = document.getElementById('orgModeDetailBtn');
    const orgModeResetBtn = document.getElementById('orgModeResetBtn');
    const orgModeToastEl = document.getElementById('orgModeToast');
    const orgModeToastBody = document.getElementById('orgModeToastBody');
    const unitKerjaName = @json($selectedUnitKerjaLabel ?? ($unitKerja->nama ?? 'Unit Kerja'));
    const selectedUnitKerjaId = @json((string) $selectedUnitKerja);
    if (orgTreeCompactContainer && orgTreeDetailContainer && orgModeCompactPanel && orgModeDetailPanel && orgModeCompactBtn && orgModeDetailBtn && orgModeResetBtn) {
        const legacyOrgModeStorageKey = 'petaJabatanOrgMode';
        const orgModeStorageKey = 'petaJabatanOrgMode:' + (selectedUnitKerjaId || 'default');

        const getSavedOrgMode = () => {
            try {
                const savedMode = localStorage.getItem(orgModeStorageKey) || localStorage.getItem(legacyOrgModeStorageKey);
                return savedMode === 'detail' ? 'detail' : 'compact';
            } catch (error) {
                return 'compact';
            }
        };

        const saveOrgMode = mode => {
            try {
                localStorage.setItem(orgModeStorageKey, mode);
            } catch (error) {
                // Ignore storage errors (private mode / disabled storage)
            }
        };

        const escapeHtml = value => String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');

        const eselonPriority = {
            'I.a': 1,
            'I.b': 2,
            'II.a': 3,
            'II.b': 4,
            'III.a': 5,
            'III.b': 6,
            'IV.a': 7,
            'IV.b': 8,
        };

        const groupedByEselon = {};

        petaJabatanChartData.forEach(item => {
            const eselonRaw = item.eselon || '';
            const eselonKey = eselonRaw !== '' ? ('Eselon ' + eselonRaw) : 'Non Eselon / Fungsional';

            if (!Object.prototype.hasOwnProperty.call(groupedByEselon, eselonKey)) {
                groupedByEselon[eselonKey] = {
                    eselonRaw: eselonRaw,
                    bezetting: 0,
                    kebutuhan: 0,
                    items: [],
                };
            }

            groupedByEselon[eselonKey].bezetting += item.bezetting;
            groupedByEselon[eselonKey].kebutuhan += item.kebutuhan;
            groupedByEselon[eselonKey].items.push(item);
        });

        const orderedGroups = Object.entries(groupedByEselon)
            .sort((a, b) => {
                const rankA = eselonPriority[a[1].eselonRaw] || 99;
                const rankB = eselonPriority[b[1].eselonRaw] || 99;

                if (rankA !== rankB) {
                    return rankA - rankB;
                }

                return a[0].localeCompare(b[0]);
            });

        const setOrgMode = mode => {
            const showCompact = mode === 'compact';

            orgModeCompactPanel.classList.toggle('d-none', !showCompact);
            orgModeDetailPanel.classList.toggle('d-none', showCompact);

            orgModeCompactBtn.classList.toggle('active', showCompact);
            orgModeDetailBtn.classList.toggle('active', !showCompact);

            saveOrgMode(showCompact ? 'compact' : 'detail');
        };

        let orgModeToastInstance = null;
        if (orgModeToastEl && window.bootstrap && bootstrap.Toast) {
            orgModeToastInstance = bootstrap.Toast.getOrCreateInstance(orgModeToastEl, {
                delay: 1800
            });
        }

        const setOrgModeToastVariant = variant => {
            if (!orgModeToastEl) {
                return;
            }

            const availableVariants = ['primary', 'success', 'warning', 'danger', 'info', 'secondary', 'dark'];
            availableVariants.forEach(item => {
                orgModeToastEl.classList.remove('text-bg-' + item);
            });

            orgModeToastEl.classList.add('text-bg-' + variant);
        };

        const showOrgModeToast = (message, variant = 'success') => {
            if (orgModeToastBody) {
                orgModeToastBody.textContent = message;
            }

            setOrgModeToastVariant(variant);

            if (orgModeToastInstance) {
                orgModeToastInstance.show();
            }
        };

        orgModeCompactBtn.addEventListener('click', function () {
            if (orgModeCompactBtn.classList.contains('active')) {
                return;
            }

            setOrgMode('compact');
            showOrgModeToast('Mode Horizontal Compact aktif.', 'primary');
        });

        orgModeDetailBtn.addEventListener('click', function () {
            if (orgModeDetailBtn.classList.contains('active')) {
                return;
            }

            setOrgMode('detail');
            showOrgModeToast('Mode Vertikal Detail aktif.', 'primary');
        });

        orgModeResetBtn.addEventListener('click', function () {
            setOrgMode('compact');
            showOrgModeToast('Mode direset ke Horizontal Compact untuk unit kerja ini.', 'success');
        });

        const getStatusClassBySelisih = selisih => {
            if (selisih > 0) {
                return 'warning';
            }

            if (selisih < 0) {
                return 'danger';
            }

            return 'success';
        };

        if (orderedGroups.length === 0) {
            orgTreeCompactContainer.innerHTML = '<p class="text-muted mb-0">Tidak ada data struktur organisasi.</p>';
            orgTreeDetailContainer.innerHTML = '<p class="text-muted mb-0">Tidak ada data struktur organisasi.</p>';
        } else {
            const renderCompactMode = () => {
                const rootUl = document.createElement('ul');
                rootUl.className = 'org-tree org-tree-compact';

                const rootLi = document.createElement('li');
                const rootNode = document.createElement('div');
                rootNode.className = 'org-node org-node-root';
                rootNode.innerHTML =
                    '<div class="org-node-title">' + escapeHtml(unitKerjaName) + '</div>' +
                    '<div class="org-node-subtitle">Unit Kerja</div>' +
                    '<div class="org-node-meta">' +
                    '<span class="badge bg-primary">Bezetting: {{ $totalBezetting }}</span>' +
                    '<span class="badge bg-info">Kebutuhan: {{ $totalKebutuhan }}</span>' +
                    '</div>';

                rootLi.appendChild(rootNode);

                const groupUl = document.createElement('ul');

                orderedGroups.forEach(([groupName, groupData]) => {
                    const groupLi = document.createElement('li');
                    const groupNode = document.createElement('div');
                    groupNode.className = 'org-node org-node-group';

                    const groupSelisih = groupData.bezetting - groupData.kebutuhan;
                    const groupStatusClass = getStatusClassBySelisih(groupSelisih);

                    groupNode.innerHTML =
                        '<div class="org-node-title">' + escapeHtml(groupName) + '</div>' +
                        '<div class="org-node-subtitle">Kelompok Jabatan</div>' +
                        '<div class="org-node-meta">' +
                        '<span class="badge bg-primary">B: ' + groupData.bezetting + '</span>' +
                        '<span class="badge bg-info">K: ' + groupData.kebutuhan + '</span>' +
                        '<span class="badge bg-' + groupStatusClass + '">S: ' + (groupSelisih >= 0 ? '+' : '') + groupSelisih + '</span>' +
                        '</div>';

                    groupLi.appendChild(groupNode);

                    const jabatanUl = document.createElement('ul');

                    groupData.items
                        .sort((a, b) => (a.kode || '').localeCompare(b.kode || ''))
                        .forEach(item => {
                            const jabatanLi = document.createElement('li');
                            const jabatanNode = document.createElement('div');
                            jabatanNode.className = 'org-node org-node-jabatan status-' + item.statusClass;

                            const titleText = (item.kode ? (item.kode + ' - ') : '') + item.label;
                            const safeTitle = titleText.length > 75 ? (titleText.substring(0, 75) + '...') : titleText;
                            const escapedTitle = escapeHtml(safeTitle);

                            let titleHtml = '<span class="org-node-title">' + escapedTitle + '</span>';
                            if (item.hasDetail && item.detailUrl) {
                                titleHtml = '<a class="org-node-link org-node-title" href="' + item.detailUrl + '">' + escapedTitle + '</a>';
                            }

                            jabatanNode.innerHTML =
                                '<div>' + titleHtml + '</div>' +
                                '<div class="org-node-meta">' +
                                '<span class="badge bg-primary">B: ' + item.bezetting + '</span>' +
                                '<span class="badge bg-info">K: ' + item.kebutuhan + '</span>' +
                                '<span class="badge bg-' + item.statusClass + '">' + item.status + '</span>' +
                                '</div>';

                            jabatanLi.appendChild(jabatanNode);
                            jabatanUl.appendChild(jabatanLi);
                        });

                    groupLi.appendChild(jabatanUl);
                    groupUl.appendChild(groupLi);
                });

                rootLi.appendChild(groupUl);
                rootUl.appendChild(rootLi);
                orgTreeCompactContainer.innerHTML = '';
                orgTreeCompactContainer.appendChild(rootUl);
            };

            const renderDetailMode = () => {
                let detailHtml =
                    '<div class="org-detail-root">' +
                    '<div class="org-detail-root-title">' + escapeHtml(unitKerjaName) + '</div>' +
                    '<div class="org-node-subtitle">Unit Kerja</div>' +
                    '<div class="org-detail-root-meta">' +
                    '<span class="badge bg-primary">Bezetting: {{ $totalBezetting }}</span>' +
                    '<span class="badge bg-info">Kebutuhan: {{ $totalKebutuhan }}</span>' +
                    '</div>';

                orderedGroups.forEach(([groupName, groupData]) => {
                    const groupSelisih = groupData.bezetting - groupData.kebutuhan;
                    const groupStatusClass = getStatusClassBySelisih(groupSelisih);

                    detailHtml +=
                        '<div class="org-detail-group">' +
                        '<div class="org-detail-group-header">' +
                        '<div class="org-detail-group-title">' + escapeHtml(groupName) + '</div>' +
                        '<div class="org-detail-group-meta">' +
                        '<span class="badge bg-primary">B: ' + groupData.bezetting + '</span>' +
                        '<span class="badge bg-info">K: ' + groupData.kebutuhan + '</span>' +
                        '<span class="badge bg-' + groupStatusClass + '">S: ' + (groupSelisih >= 0 ? '+' : '') + groupSelisih + '</span>' +
                        '</div>' +
                        '</div>' +
                        '<div class="table-responsive">' +
                        '<table class="table table-sm table-bordered org-detail-table">' +
                        '<thead>' +
                        '<tr>' +
                        '<th width="40">No</th>' +
                        '<th>Kode</th>' +
                        '<th>Nama Jabatan</th>' +
                        '<th class="text-center" width="80">B</th>' +
                        '<th class="text-center" width="80">K</th>' +
                        '<th class="text-center" width="90">Selisih</th>' +
                        '<th class="text-center" width="110">Status</th>' +
                        '</tr>' +
                        '</thead>' +
                        '<tbody>';

                    groupData.items
                        .sort((a, b) => (a.kode || '').localeCompare(b.kode || ''))
                        .forEach((item, index) => {
                            const selisih = item.bezetting - item.kebutuhan;
                            const titleText = item.label || '-';
                            const escapedTitle = escapeHtml(titleText);
                            let nameHtml = escapedTitle;

                            if (item.hasDetail && item.detailUrl) {
                                nameHtml = '<a class="org-node-link" href="' + escapeHtml(item.detailUrl) + '">' + escapedTitle + '</a>';
                            }

                            detailHtml +=
                                '<tr>' +
                                '<td class="text-center">' + (index + 1) + '</td>' +
                                '<td>' + escapeHtml(item.kode || '-') + '</td>' +
                                '<td>' + nameHtml + '</td>' +
                                '<td class="text-center"><span class="badge bg-primary">' + item.bezetting + '</span></td>' +
                                '<td class="text-center"><span class="badge bg-info">' + item.kebutuhan + '</span></td>' +
                                '<td class="text-center">' + (selisih >= 0 ? '+' : '') + selisih + '</td>' +
                                '<td class="text-center"><span class="badge bg-' + item.statusClass + '">' + escapeHtml(item.status) + '</span></td>' +
                                '</tr>';
                        });

                    detailHtml +=
                        '</tbody>' +
                        '</table>' +
                        '</div>' +
                        '</div>';
                });

                detailHtml += '</div>';
                orgTreeDetailContainer.innerHTML = detailHtml;
            };

            renderCompactMode();
            renderDetailMode();
        }
        setOrgMode(getSavedOrgMode());
    }
}
</script>
@endpush
@endif
