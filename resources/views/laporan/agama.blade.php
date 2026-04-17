@extends('layouts.app')

@section('title', 'Laporan Berdasarkan Agama')

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
                    <li class="breadcrumb-item active">Berdasarkan Agama</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-pray me-2"></i>Laporan Berdasarkan Agama
            </h1>
        </div>
    </div>

    <div class="row">
        <!-- Pie Chart -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Grafik Distribusi
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="agamaChart" height="300"></canvas>
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
                    <canvas id="agamaBarChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table me-2"></i>Data per Agama
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th width="50">No</th>
                            <th>Agama</th>
                            <th width="150">Jumlah Pegawai</th>
                            <th width="150">Persentase</th>
                            <th>Visual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = $data->sum('total'); @endphp
                        @forelse($data as $index => $item)
                            @php $persentase = $total > 0 ? ($item->total / $total) * 100 : 0; @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $item->agama }}</strong></td>
                                <td class="text-center">
                                    <span class="badge bg-primary fs-6">{{ number_format($item->total) }}</span>
                                </td>
                                <td class="text-center">{{ number_format($persentase, 2) }}%</td>
                                <td>
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ $persentase }}%">
                                            {{ number_format($persentase, 1) }}%
                                        </div>
                                    </div>
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
                            <th colspan="2" class="text-end">Total</th>
                            <th class="text-center">{{ number_format($total) }}</th>
                            <th class="text-center">100%</th>
                            <th></th>
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
const ctx = document.getElementById('agamaChart').getContext('2d');
new Chart(ctx, {
    type: 'pie',
    data: {
        labels: chartData.map(item => item.label),
        datasets: [{
            data: chartData.map(item => item.value),
            backgroundColor: [
                '#1cc88a', '#4e73df', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'
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

// Bar Chart
const ctx2 = document.getElementById('agamaBarChart').getContext('2d');
new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: chartData.map(item => item.label),
        datasets: [{
            label: 'Jumlah Pegawai',
            data: chartData.map(item => item.value),
            backgroundColor: [
                '#1cc88a', '#4e73df', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'
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
