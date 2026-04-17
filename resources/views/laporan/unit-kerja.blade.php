@extends('layouts.app')

@section('title', 'Laporan Berdasarkan Unit Kerja')

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
                    <li class="breadcrumb-item active">Berdasarkan Unit Kerja</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-building me-2"></i>Laporan Berdasarkan Unit Kerja
            </h1>
        </div>
    </div>

    <div class="row">
        <!-- Chart -->
        <div class="col-xl-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>Distribusi per Unit Kerja
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="unitKerjaChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table me-2"></i>Data per Unit Kerja ({{ $data->count() }} unit kerja)
            </h6>
            <div>
                <a href="{{ route('laporan.export', ['type' => 'unit_kerja']) }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel me-1"></i> Export Excel
                </a>
                <a href="{{ route('laporan.print', ['type' => 'unit_kerja']) }}" target="_blank" class="btn btn-secondary btn-sm">
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
                            <th>Nama Unit Kerja</th>
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
                                <td>{{ $item->nama }}</td>
                                <td class="text-center">
                                    <span class="badge bg-warning text-dark">{{ number_format($item->pegawai_count) }}</span>
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
                                <td colspan="5" class="text-center">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <th colspan="3" class="text-end">Total</th>
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

const ctx = document.getElementById('unitKerjaChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: chartData.map(item => item.label.length > 40 ? item.label.substring(0, 40) + '...' : item.label),
        datasets: [{
            label: 'Jumlah Pegawai',
            data: chartData.map(item => item.value),
            backgroundColor: '#f6c23e',
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
