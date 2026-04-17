@extends('layouts.app')

@section('title', 'Laporan Berdasarkan Golongan')

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
                    <li class="breadcrumb-item active">Berdasarkan Golongan</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-layer-group me-2"></i>Laporan Berdasarkan Golongan
            </h1>
        </div>
    </div>

    <div class="row">
        <!-- Chart -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Grafik Distribusi
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="golonganChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Bar Chart -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>Grafik Batang
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="golonganBarChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table me-2"></i>Data per Golongan
            </h6>
            <div>
                <a href="{{ route('laporan.export', ['type' => 'golongan']) }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel me-1"></i> Export Excel
                </a>
                <a href="{{ route('laporan.print', ['type' => 'golongan']) }}" target="_blank" class="btn btn-secondary btn-sm">
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
                            <th>Nama Golongan</th>
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
                                    <span class="badge bg-primary">{{ number_format($item->pegawai_count) }}</span>
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
const chartData = @json($chartData);

// Pie Chart
const ctx = document.getElementById('golonganChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: chartData.map(item => item.label),
        datasets: [{
            data: chartData.map(item => item.value),
            backgroundColor: [
                '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                '#858796', '#5a5c69', '#2e59d9', '#17a673', '#2c9faf',
                '#dda20a', '#c42a1c', '#6f42c1', '#20c997', '#fd7e14'
            ],
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                display: chartData.length <= 10
            }
        }
    }
});

// Bar Chart
const ctx2 = document.getElementById('golonganBarChart').getContext('2d');
new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: chartData.slice(0, 15).map(item => item.label),
        datasets: [{
            label: 'Jumlah Pegawai',
            data: chartData.slice(0, 15).map(item => item.value),
            backgroundColor: '#4e73df',
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
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
