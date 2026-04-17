@extends('layouts.app')

@section('title', 'Laporan Berdasarkan Eselon')

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
                    <li class="breadcrumb-item active">Berdasarkan Eselon</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-sitemap me-2"></i>Laporan Berdasarkan Eselon & Status Pegawai
            </h1>
        </div>
    </div>

    <div class="row">
        <!-- Pie Chart - Eselon -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Distribusi per Eselon
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="eselonChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Bar Chart - Status -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>Distribusi per Status Pegawai
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Cross-Tabulation -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table me-2"></i>Rekapitulasi Pegawai per Eselon dan Status
            </h6>
            <div>
                <a href="{{ route('laporan.export', ['type' => 'eselon']) }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel me-1"></i> Export Excel
                </a>
                <a href="{{ route('laporan.print', ['type' => 'eselon']) }}" target="_blank" class="btn btn-secondary btn-sm">
                    <i class="fas fa-print me-1"></i> Print
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th rowspan="2" class="align-middle text-center" width="50">No</th>
                            <th rowspan="2" class="align-middle text-center">Eselon</th>
                            <th colspan="{{ count($statusList) }}" class="text-center">Status Pegawai</th>
                            <th rowspan="2" class="align-middle text-center">Total</th>
                        </tr>
                        <tr>
                            @foreach($statusList as $status)
                            <th class="text-center">{{ $status }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach($eselonList as $eselon)
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td><strong>{{ $eselon }}</strong></td>
                                @foreach($statusList as $status)
                                <td class="text-center">
                                    @if($data[$eselon][$status] > 0)
                                    <span class="badge bg-primary">{{ number_format($data[$eselon][$status]) }}</span>
                                    @else
                                    <span class="text-muted">0</span>
                                    @endif
                                </td>
                                @endforeach
                                <td class="text-center">
                                    <span class="badge bg-success">{{ number_format($totalPerEselon[$eselon]) }}</span>
                                </td>
                            </tr>
                        @endforeach
                        
                        <!-- Tanpa Eselon -->
                        @if($totalNoEselon > 0)
                        <tr class="table-warning">
                            <td class="text-center">{{ $no++ }}</td>
                            <td><em>Tanpa Eselon</em></td>
                            @foreach($statusList as $status)
                            <td class="text-center">
                                @if($dataNoEselon[$status] > 0)
                                <span class="badge bg-secondary">{{ number_format($dataNoEselon[$status]) }}</span>
                                @else
                                <span class="text-muted">0</span>
                                @endif
                            </td>
                            @endforeach
                            <td class="text-center">
                                <span class="badge bg-warning text-dark">{{ number_format($totalNoEselon) }}</span>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <th colspan="2" class="text-center">Total</th>
                            @foreach($statusList as $status)
                            <th class="text-center">{{ number_format($totalPerStatus[$status] ?? 0) }}</th>
                            @endforeach
                            <th class="text-center">
                                <span class="badge bg-dark">{{ number_format($grandTotal) }}</span>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        @foreach($eselonList as $eselon)
        <div class="col-xl-2 col-md-4 col-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">{{ $eselon }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalPerEselon[$eselon]) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        
        @if($totalNoEselon > 0)
        <div class="col-xl-2 col-md-4 col-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Tanpa Eselon</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalNoEselon) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const chartDataEselon = @json($chartDataEselon);
const chartDataStatus = @json($chartDataStatus);

// Pie Chart - Eselon
const ctx1 = document.getElementById('eselonChart').getContext('2d');
new Chart(ctx1, {
    type: 'doughnut',
    data: {
        labels: chartDataEselon.map(item => item.label),
        datasets: [{
            data: chartDataEselon.map(item => item.value),
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
                position: 'bottom'
            }
        }
    }
});

// Bar Chart - Status
const ctx2 = document.getElementById('statusChart').getContext('2d');
new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: chartDataStatus.map(item => item.label),
        datasets: [{
            label: 'Jumlah Pegawai',
            data: chartDataStatus.map(item => item.value),
            backgroundColor: [
                '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                '#858796', '#5a5c69'
            ],
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
</script>
@endpush
