@extends('layouts.app')

@section('title', 'Statistik Kepegawaian')

@section('content')
<div class="container-fluid">
    @if(isset($unitKerja) && $unitKerja)
    <div class="alert alert-info mb-4">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Sub Admin:</strong> Statistik menampilkan data pegawai dari unit kerja <strong>{{ $unitKerja->nama }}</strong>
    </div>
    @endif
    
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
                    <li class="breadcrumb-item active">Statistik</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-pie me-2"></i>Statistik Kepegawaian
            </h1>
        </div>
    </div>

    <!-- Statistik Utama -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pegawai</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_pegawai']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Laki-laki</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['laki_laki']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-male fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Perempuan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['perempuan']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-female fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">PNS</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['pns']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-id-badge fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik -->
    <div class="row">
        <!-- Jenis Kelamin -->
        <div class="col-xl-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-venus-mars me-2"></i>Distribusi Jenis Kelamin
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="genderChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Status Pegawai -->
        <div class="col-xl-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-id-badge me-2"></i>Distribusi Status Pegawai
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Status Pegawai Detail -->
        <div class="col-xl-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>Detail Status Pegawai
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>PNS</span>
                            <span class="badge bg-success">{{ number_format($stats['pns']) }}</span>
                        </div>
                        <div class="progress mt-1" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: {{ $stats['total_pegawai'] > 0 ? ($stats['pns'] / $stats['total_pegawai']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>CPNS</span>
                            <span class="badge bg-info">{{ number_format($stats['cpns']) }}</span>
                        </div>
                        <div class="progress mt-1" style="height: 10px;">
                            <div class="progress-bar bg-info" style="width: {{ $stats['total_pegawai'] > 0 ? ($stats['cpns'] / $stats['total_pegawai']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>PPPK</span>
                            <span class="badge bg-warning">{{ number_format($stats['pppk']) }}</span>
                        </div>
                        <div class="progress mt-1" style="height: 10px;">
                            <div class="progress-bar bg-warning" style="width: {{ $stats['total_pegawai'] > 0 ? ($stats['pppk'] / $stats['total_pegawai']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>PPPK Paruh Waktu</span>
                            <span class="badge" style="background-color: #fd7e14;">{{ number_format($stats['pppk_paruh_waktu']) }}</span>
                        </div>
                        <div class="progress mt-1" style="height: 10px;">
                            <div class="progress-bar" style="background-color: #fd7e14; width: {{ $stats['total_pegawai'] > 0 ? ($stats['pppk_paruh_waktu'] / $stats['total_pegawai']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Non ASN</span>
                            <span class="badge bg-secondary">{{ number_format($stats['non_asn']) }}</span>
                        </div>
                        <div class="progress mt-1" style="height: 10px;">
                            <div class="progress-bar bg-secondary" style="width: {{ $stats['total_pegawai'] > 0 ? ($stats['non_asn'] / $stats['total_pegawai']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Berhenti/Keluar</span>
                            <span class="badge bg-danger">{{ number_format($stats['berhenti_keluar']) }}</span>
                        </div>
                        <div class="progress mt-1" style="height: 10px;">
                            <div class="progress-bar bg-danger" style="width: {{ $stats['total_pegawai'] > 0 ? ($stats['berhenti_keluar'] / $stats['total_pegawai']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Pensiun</span>
                            <span class="badge bg-dark">{{ number_format($stats['pensiun']) }}</span>
                        </div>
                        <div class="progress mt-1" style="height: 10px;">
                            <div class="progress-bar bg-dark" style="width: {{ $stats['total_pegawai'] > 0 ? ($stats['pensiun'] / $stats['total_pegawai']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Golongan & Jabatan -->
    <div class="row">
        <div class="col-xl-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-layer-group me-2"></i>Top 10 Golongan
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="golonganChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-briefcase me-2"></i>Top 10 Jabatan
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="jabatanChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Button -->
    <div class="row">
        <div class="col-12 text-center">
            <a href="{{ route('laporan.print', ['type' => 'statistik']) }}" target="_blank" class="btn btn-primary btn-lg">
                <i class="fas fa-print me-2"></i> Cetak Laporan Statistik
            </a>
        </div>
    </div>
</div>

<style>
.border-left-primary { border-left: 4px solid #4e73df !important; }
.border-left-success { border-left: 4px solid #1cc88a !important; }
.border-left-danger { border-left: 4px solid #e74a3b !important; }
.border-left-info { border-left: 4px solid #36b9cc !important; }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const genderData = @json($genderData);
const statusData = @json($statusData);
const topGolongan = @json($topGolongan);
const topJabatan = @json($topJabatan);

// Gender Chart
new Chart(document.getElementById('genderChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: genderData.map(item => item.label),
        datasets: [{
            data: genderData.map(item => item.value),
            backgroundColor: ['#4e73df', '#e74a3b'],
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

// Status Chart
new Chart(document.getElementById('statusChart').getContext('2d'), {
    type: 'pie',
    data: {
        labels: statusData.map(item => item.label),
        datasets: [{
            data: statusData.map(item => item.value),
            backgroundColor: ['#1cc88a', '#36b9cc', '#f6c23e', '#858796'],
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

// Golongan Chart
new Chart(document.getElementById('golonganChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: topGolongan.map(item => item.label.length > 15 ? item.label.substring(0, 15) + '...' : item.label),
        datasets: [{
            label: 'Jumlah',
            data: topGolongan.map(item => item.value),
            backgroundColor: '#4e73df',
        }]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true } },
        plugins: { legend: { display: false } }
    }
});

// Jabatan Chart
new Chart(document.getElementById('jabatanChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: topJabatan.map(item => item.label.length > 15 ? item.label.substring(0, 15) + '...' : item.label),
        datasets: [{
            label: 'Jumlah',
            data: topJabatan.map(item => item.value),
            backgroundColor: '#1cc88a',
        }]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true } },
        plugins: { legend: { display: false } }
    }
});
</script>
@endpush
