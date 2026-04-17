@extends('layouts.app')

@section('title', $rekapTitle ?? 'Rekap Peta Jabatan')
@section('page-title', $rekapTitle ?? 'Rekap Peta Jabatan Semua Unit Kerja')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-bar-chart me-2"></i>Rekap Peta Jabatan
            @if(!empty($rekapBadge))
                <span class="badge bg-secondary ms-2">{{ $rekapBadge }}</span>
            @endif
        </span>
        <div>
            <a href="{{ route('peta-jabatan.rekap-print', ['scope' => $rekapScope ?? 'all']) }}" class="btn btn-outline-secondary btn-sm me-1" target="_blank">
                <i class="bi bi-printer me-1"></i>Cetak Rekap
            </a>
            <a href="{{ route('peta-jabatan.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-card bg-primary position-relative">
                    <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                    <h6 class="text-white-50 mb-1">Total Bezetting</h6>
                    <h2 class="mb-0">{{ $grandTotalBezetting }}</h2>
                    <small>Seluruh Pegawai Aktif</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card bg-info position-relative">
                    <div class="stat-icon"><i class="bi bi-clipboard-check"></i></div>
                    <h6 class="text-white-50 mb-1">Total Kebutuhan</h6>
                    <h2 class="mb-0">{{ $grandTotalKebutuhan }}</h2>
                    <small>Seluruh Kebutuhan</small>
                </div>
            </div>
            <div class="col-md-4">
                @php
                    $grandSelisih = $grandTotalBezetting - $grandTotalKebutuhan;
                    $statusClass = $grandSelisih == 0 ? 'success' : ($grandSelisih > 0 ? 'warning' : 'danger');
                @endphp
                <div class="stat-card bg-{{ $statusClass }} position-relative">
                    <div class="stat-icon"><i class="bi bi-graph-{{ $grandSelisih >= 0 ? 'up' : 'down' }}"></i></div>
                    <h6 class="text-white-50 mb-1">Total Selisih</h6>
                    <h2 class="mb-0">{{ $grandSelisih >= 0 ? '+' : '' }}{{ $grandSelisih }}</h2>
                    <small>{{ $grandSelisih == 0 ? 'Terpenuhi' : ($grandSelisih > 0 ? 'Kelebihan' : 'Kekurangan') }}</small>
                </div>
            </div>
        </div>

        <!-- Rekap Table -->
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead class="table-light">
                    <tr>
                        <th width="50" class="text-center">No</th>
                        <th>Unit Kerja</th>
                        <th class="text-center" width="120">Bezetting</th>
                        <th class="text-center" width="120">Kebutuhan</th>
                        <th class="text-center" width="120">Selisih</th>
                        <th class="text-center" width="130">Status</th>
                        <th class="text-center" width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapData as $i => $item)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $item['unit_kerja']->nama }}</td>
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
                        <td class="text-center">
                            <a href="{{ route('peta-jabatan.index', ['unit_kerja_id' => $item['unit_kerja']->getRouteKey()]) }}" 
                               class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Tidak ada data unit kerja.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($rekapData) > 0)
                <tfoot class="table-light">
                    <tr>
                        <th colspan="2" class="text-end">Grand Total:</th>
                        <th class="text-center">{{ $grandTotalBezetting }}</th>
                        <th class="text-center">{{ $grandTotalKebutuhan }}</th>
                        <th class="text-center">{{ $grandSelisih >= 0 ? '+' : '' }}{{ $grandSelisih }}</th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<!-- Legend -->
<div class="card mt-3">
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
    </div>
</div>
@endsection
