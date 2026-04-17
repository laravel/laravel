@extends('layouts.app')

@section('title', 'Laporan Berdasarkan Jabatan')

@section('content')
<div class="container-fluid">
    @if(isset($unitKerja) && $unitKerja)
    <div class="alert alert-info mb-4">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Sub Admin:</strong> Data menampilkan pegawai dari unit kerja <strong>{{ $unitKerja->nama }}</strong>
    </div>
    @endif
    
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
                    <li class="breadcrumb-item active">Berdasarkan Jabatan</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-briefcase me-2"></i>Laporan Berdasarkan Jabatan
            </h1>
            <div class="mt-3 d-flex flex-wrap gap-2">
                <a href="{{ route('laporan.jabatan') }}" class="btn btn-sm {{ ($reportScope ?? 'all') === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="fas fa-table me-1"></i>Rekap Semua Unit
                </a>
                @if(!auth()->user()->isSubAdmin())
                <a href="{{ route('laporan.jabatan', ['scope' => 'uptd-puskesmas']) }}" class="btn btn-sm {{ ($reportScope ?? 'all') === 'uptd-puskesmas' ? 'btn-info' : 'btn-outline-info' }}">
                    <i class="fas fa-hospital me-1"></i>Rekap UPTD Puskesmas
                </a>
                @endif
                <span class="badge bg-secondary align-self-center">{{ $scopeLabel ?? 'Rekap Semua Unit' }}</span>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filter Unit Kerja
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('laporan.jabatan') }}" class="row align-items-end">
                @if(($reportScope ?? 'all') === 'uptd-puskesmas')
                    <input type="hidden" name="scope" value="uptd-puskesmas">
                @endif
                <div class="col-md-5 mb-3">
                    <label class="form-label">Unit Kerja</label>
                    <select name="unit_kerja_id" class="form-select select2" data-placeholder="-- Semua Unit Kerja --" {{ isset($unitKerja) && auth()->user()->isSubAdmin() ? 'disabled' : '' }}>
                        <option value=""></option>
                        @foreach($unitKerjas as $uk)
                            <option value="{{ $uk->getRouteKey() }}" {{ (string) $selectedUnitKerja === (string) $uk->id || (string) ($selectedUnitKerjaParam ?? '') === (string) $uk->getRouteKey() ? 'selected' : '' }}>{{ $uk->nama }}</option>
                        @endforeach
                    </select>
                    @if(isset($unitKerja) && auth()->user()->isSubAdmin())
                        <small class="text-muted">Sub Admin hanya dapat melihat unit kerja sendiri.</small>
                    @endif
                </div>
                <div class="col-md-3 mb-3">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i>Terapkan
                    </button>
                    <a href="{{ route('laporan.jabatan', ($reportScope ?? 'all') === 'uptd-puskesmas' ? ['scope' => 'uptd-puskesmas'] : []) }}" class="btn btn-secondary">
                        <i class="fas fa-sync me-1"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Chart -->
        <div class="col-xl-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>Top 20 Jabatan
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="jabatanChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table me-2"></i>Data per Jabatan dan Unit Kerja ({{ $data->count() }} baris)
            </h6>
            <div>
                <a href="{{ route('laporan.export', array_filter(array_merge(
                    ['type' => 'jabatan'],
                    ['unit_kerja_id' => $selectedUnitKerjaParam ?? null],
                    ['scope' => ($reportScope ?? 'all') === 'uptd-puskesmas' ? 'uptd-puskesmas' : null]
                ))) }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel me-1"></i> Export Excel
                </a>
                <a href="{{ route('laporan.print', array_filter(array_merge(
                    ['type' => 'jabatan'],
                    ['unit_kerja_id' => $selectedUnitKerjaParam ?? null],
                    ['scope' => ($reportScope ?? 'all') === 'uptd-puskesmas' ? 'uptd-puskesmas' : null]
                ))) }}" target="_blank" class="btn btn-secondary btn-sm">
                    <i class="fas fa-print me-1"></i> Print
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="dataTable">
                    <thead class="table-dark">
                        <tr>
                            <th width="50">No</th>
                            <th>Kode</th>
                            <th>Nama Jabatan</th>
                            <th>Unit Kerja</th>
                            <th width="150">Jumlah Pegawai</th>
                            <th width="150">Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalPegawai = $data->sum('pegawai_count'); @endphp
                        @forelse($data as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->kode }}</td>
                                <td>{{ $item->jabatan_nama }}</td>
                                <td>{{ $item->unit_kerja_nama ?? 'Tanpa Unit Kerja' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ number_format($item->pegawai_count) }}</span>
                                </td>
                                <td class="text-center">
                                    @if($totalPegawai > 0)
                                        {{ number_format(($item->pegawai_count / $totalPegawai) * 100, 2) }}%
                                    @else
                                        0%
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <th colspan="4" class="text-end">Total</th>
                            <th class="text-center">{{ number_format($totalPegawai) }}</th>
                            <th class="text-center">100%</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const chartData = @json($chartData->sortByDesc('value')->take(20)->values());

const ctx = document.getElementById('jabatanChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: chartData.map(item => item.label.length > 30 ? item.label.substring(0, 30) + '...' : item.label),
        datasets: [{
            label: 'Jumlah Pegawai',
            data: chartData.map(item => item.value),
            backgroundColor: '#36b9cc',
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        scales: {
            x: {
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

$('#dataTable').DataTable({
    language: {
        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
    },
    pageLength: 25,
    order: [[3, 'desc']]
});
</script>
@endpush
