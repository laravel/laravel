@extends('layouts.app')

@section('title', 'Laporan Pegawai Akan Pensiun')

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
                    <li class="breadcrumb-item active">Pegawai Akan Pensiun</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-clock me-2"></i>Laporan Pegawai Akan Pensiun
            </h1>
        </div>
    </div>

    <!-- Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filter Tahun Pensiun
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('laporan.pensiun') }}" class="row align-items-end">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Tahun Pensiun</label>
                    <select name="tahun" class="form-select">
                        @foreach($tahunOptions as $tahun)
                            <option value="{{ $tahun }}" {{ $tahunPensiun == $tahun ? 'selected' : '' }}>
                                {{ $tahun }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Info -->
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        Batas usia pensiun: <strong>{{ $batasPensiun }} tahun</strong>. 
        Menampilkan pegawai yang akan pensiun pada tahun <strong>{{ $tahunPensiun }}</strong>.
        <br>
        <small class="text-muted">Total: {{ $pegawai->count() }} pegawai akan pensiun.</small>
    </div>

    <!-- Tabel Data -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table me-2"></i>Daftar Pegawai Akan Pensiun Tahun {{ $tahunPensiun }} ({{ $pegawai->count() }} orang)
            </h6>
            <div>
                <a href="{{ route('laporan.export', ['type' => 'pensiun', 'tahun' => $tahunPensiun]) }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel me-1"></i> Export Excel
                </a>
                <a href="{{ route('laporan.print', ['type' => 'pensiun', 'tahun' => $tahunPensiun]) }}" target="_blank" class="btn btn-secondary btn-sm">
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
                            <th>NIP</th>
                            <th>Nama</th>
                            <th>Tanggal Lahir</th>
                            <th>Usia</th>
                            <th>Golongan</th>
                            <th>Jabatan</th>
                            <th>Unit Kerja</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pegawai as $index => $p)
                            @php
                                $usia = \Carbon\Carbon::parse($p->tanggal_lahir)->age;
                                $sisaBulan = $batasPensiun * 12 - $usia * 12 - \Carbon\Carbon::parse($p->tanggal_lahir)->month + now()->month;
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $p->nip }}</td>
                                <td>{{ $p->nama }}</td>
                                <td>{{ \Carbon\Carbon::parse($p->tanggal_lahir)->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $usia >= 57 ? 'danger' : 'warning' }}">
                                        {{ $usia }} tahun
                                    </span>
                                </td>
                                <td>{{ $p->golongan?->nama ?? '-' }}</td>
                                <td>{{ Str::limit($p->jabatan?->nama ?? '-', 25) }}</td>
                                <td>{{ Str::limit($p->unitKerja?->nama ?? '-', 25) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada pegawai yang akan pensiun pada tahun {{ $tahunPensiun }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
        },
        pageLength: 25,
        order: [[3, 'asc']]
    });
});
</script>
@endpush
