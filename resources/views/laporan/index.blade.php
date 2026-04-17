@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="container-fluid">
    @if(isset($unitKerja) && $unitKerja)
    <div class="alert alert-info mb-4">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Sub Admin:</strong> Data laporan menampilkan pegawai dari unit kerja <strong>{{ $unitKerja->nama }}</strong>
    </div>
    @endif
    
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-bar me-2"></i>Modul Laporan
            </h1>
            <p class="text-muted">Pilih jenis laporan yang ingin dilihat</p>
        </div>
    </div>

    <!-- Statistik Ringkas -->
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Golongan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_golongan']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-layer-group fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Jabatan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_jabatan']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-briefcase fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Unit Kerja</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_unit_kerja']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Laporan -->
    <div class="row">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-users fa-3x text-primary"></i>
                    </div>
                    <h5 class="card-title">Laporan Data Pegawai</h5>
                    <p class="card-text text-muted">Lihat daftar lengkap pegawai dengan filter berdasarkan status, golongan, jabatan, dan unit kerja.</p>
                    <a href="{{ route('laporan.pegawai') }}" class="btn btn-primary">
                        <i class="fas fa-eye me-1"></i> Lihat Laporan
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-layer-group fa-3x text-success"></i>
                    </div>
                    <h5 class="card-title">Laporan Berdasarkan Golongan</h5>
                    <p class="card-text text-muted">Lihat distribusi pegawai berdasarkan golongan/pangkat dengan grafik visual.</p>
                    <a href="{{ route('laporan.golongan') }}" class="btn btn-success">
                        <i class="fas fa-eye me-1"></i> Lihat Laporan
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-briefcase fa-3x text-info"></i>
                    </div>
                    <h5 class="card-title">Laporan Berdasarkan Jabatan</h5>
                    <p class="card-text text-muted">Lihat distribusi pegawai berdasarkan jabatan dengan grafik visual.</p>
                    <a href="{{ route('laporan.jabatan') }}" class="btn btn-info">
                        <i class="fas fa-eye me-1"></i> Lihat Laporan
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-sitemap fa-3x text-indigo"></i>
                    </div>
                    <h5 class="card-title">Laporan Berdasarkan Eselon</h5>
                    <p class="card-text text-muted">Lihat distribusi pegawai berdasarkan eselon dan status pegawai dengan tabel cross-tabulation.</p>
                    <a href="{{ route('laporan.eselon') }}" class="btn btn-primary">
                        <i class="fas fa-eye me-1"></i> Lihat Laporan
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-building fa-3x text-warning"></i>
                    </div>
                    <h5 class="card-title">Laporan Berdasarkan Unit Kerja</h5>
                    <p class="card-text text-muted">Lihat distribusi pegawai berdasarkan unit kerja/OPD.</p>
                    <a href="{{ route('laporan.unit-kerja') }}" class="btn btn-warning">
                        <i class="fas fa-eye me-1"></i> Lihat Laporan
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-user-clock fa-3x text-danger"></i>
                    </div>
                    <h5 class="card-title">Laporan Pegawai Akan Pensiun</h5>
                    <p class="card-text text-muted">Lihat daftar pegawai yang akan memasuki masa pensiun.</p>
                    <a href="{{ route('laporan.pensiun') }}" class="btn btn-danger">
                        <i class="fas fa-eye me-1"></i> Lihat Laporan
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-birthday-cake fa-3x text-secondary"></i>
                    </div>
                    <h5 class="card-title">Laporan Berdasarkan Usia</h5>
                    <p class="card-text text-muted">Lihat distribusi pegawai berdasarkan kelompok usia.</p>
                    <a href="{{ route('laporan.usia') }}" class="btn btn-secondary">
                        <i class="fas fa-eye me-1"></i> Lihat Laporan
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-graduation-cap fa-3x text-purple"></i>
                    </div>
                    <h5 class="card-title">Laporan Berdasarkan Pendidikan</h5>
                    <p class="card-text text-muted">Lihat distribusi pegawai berdasarkan tingkat pendidikan.</p>
                    <a href="{{ route('laporan.pendidikan') }}" class="btn btn-dark">
                        <i class="fas fa-eye me-1"></i> Lihat Laporan
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-pray fa-3x text-teal"></i>
                    </div>
                    <h5 class="card-title">Laporan Berdasarkan Agama</h5>
                    <p class="card-text text-muted">Lihat distribusi pegawai berdasarkan agama.</p>
                    <a href="{{ route('laporan.agama') }}" class="btn btn-outline-primary">
                        <i class="fas fa-eye me-1"></i> Lihat Laporan
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-chart-pie fa-3x text-indigo"></i>
                    </div>
                    <h5 class="card-title">Statistik Keseluruhan</h5>
                    <p class="card-text text-muted">Lihat statistik dan grafik keseluruhan data kepegawaian.</p>
                    <a href="{{ route('laporan.statistik') }}" class="btn btn-outline-success">
                        <i class="fas fa-eye me-1"></i> Lihat Laporan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-venus-mars me-2"></i>Distribusi Jenis Kelamin
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 text-center">
                            <i class="fas fa-male fa-3x text-primary mb-2"></i>
                            <h4>{{ number_format($stats['laki_laki']) }}</h4>
                            <p class="text-muted">Laki-laki</p>
                        </div>
                        <div class="col-6 text-center">
                            <i class="fas fa-female fa-3x text-danger mb-2"></i>
                            <h4>{{ number_format($stats['perempuan']) }}</h4>
                            <p class="text-muted">Perempuan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-id-badge me-2"></i>Distribusi Status Pegawai
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col text-center">
                            <h5 class="text-success">{{ number_format($stats['pns']) }}</h5>
                            <small class="text-muted">PNS</small>
                        </div>
                        <div class="col text-center">
                            <h5 class="text-info">{{ number_format($stats['cpns']) }}</h5>
                            <small class="text-muted">CPNS</small>
                        </div>
                        <div class="col text-center">
                            <h5 class="text-warning">{{ number_format($stats['pppk']) }}</h5>
                            <small class="text-muted">PPPK</small>
                        </div>
                        <div class="col text-center">
                            <h5 class="text-orange">{{ number_format($stats['pppk_paruh_waktu']) }}</h5>
                            <small class="text-muted">PPPK PW</small>
                        </div>
                        <div class="col text-center">
                            <h5 class="text-secondary">{{ number_format($stats['non_asn']) }}</h5>
                            <small class="text-muted">Non ASN</small>
                        </div>
                        <div class="col text-center">
                            <h5 class="text-danger">{{ number_format($stats['berhenti_keluar']) }}</h5>
                            <small class="text-muted">Berhenti</small>
                        </div>
                        <div class="col text-center">
                            <h5 class="text-dark">{{ number_format($stats['pensiun']) }}</h5>
                            <small class="text-muted">Pensiun</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary { border-left: 4px solid #4e73df !important; }
.border-left-success { border-left: 4px solid #1cc88a !important; }
.border-left-info { border-left: 4px solid #36b9cc !important; }
.border-left-warning { border-left: 4px solid #f6c23e !important; }
.text-purple { color: #6f42c1 !important; }
.text-teal { color: #20c997 !important; }
.text-indigo { color: #6610f2 !important; }
.text-orange { color: #fd7e14 !important; }
</style>
@endsection
