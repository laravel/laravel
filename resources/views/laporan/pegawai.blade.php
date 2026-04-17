@extends('layouts.app')

@section('title', 'Laporan Data Pegawai')

@section('content')
<div class="container-fluid">
    @if(isset($unitKerjaInfo) && $unitKerjaInfo)
    <div class="alert alert-info mb-4">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Sub Admin:</strong> Data menampilkan pegawai dari unit kerja <strong>{{ $unitKerjaInfo->nama }}</strong>
    </div>
    @endif
    
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li>
                    <li class="breadcrumb-item active">Data Pegawai</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-users me-2"></i>Laporan Data Pegawai
            </h1>
        </div>
    </div>

    <!-- Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filter Data
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('laporan.pegawai') }}">
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Status Pegawai</label>
                        <select name="status_pegawai" class="form-select">
                            <option value="">Semua</option>
                            <option value="PNS" {{ request('status_pegawai') == 'PNS' ? 'selected' : '' }}>PNS</option>
                            <option value="CPNS" {{ request('status_pegawai') == 'CPNS' ? 'selected' : '' }}>CPNS</option>
                            <option value="PPPK" {{ request('status_pegawai') == 'PPPK' ? 'selected' : '' }}>PPPK</option>
                            <option value="PPPK Paruh Waktu" {{ request('status_pegawai') == 'PPPK Paruh Waktu' ? 'selected' : '' }}>PPPK Paruh Waktu</option>
                            <option value="Non ASN" {{ request('status_pegawai') == 'Non ASN' ? 'selected' : '' }}>Non ASN</option>
                            <option value="Berhenti/Keluar" {{ request('status_pegawai') == 'Berhenti/Keluar' ? 'selected' : '' }}>Berhenti/Keluar</option>
                            <option value="Pensiun" {{ request('status_pegawai') == 'Pensiun' ? 'selected' : '' }}>Pensiun</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select">
                            <option value="">Semua</option>
                            <option value="L" {{ request('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ request('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Golongan</label>
                        <select name="golongan_id" class="form-select select2" data-placeholder="Semua">
                            <option value=""></option>
                            @foreach($golongan as $g)
                                <option value="{{ $g->getRouteKey() }}" {{ (string) request('golongan_id') === (string) ($selectedGolonganParam ?? '') || (string) request('golongan_id') === (string) $g->id ? 'selected' : '' }}>
                                    {{ $g->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Jabatan</label>
                        <select name="jabatan_id" class="form-select select2" data-placeholder="Semua">
                            <option value=""></option>
                            @foreach($jabatan as $j)
                                <option value="{{ $j->getRouteKey() }}" {{ (string) request('jabatan_id') === (string) ($selectedJabatanParam ?? '') || (string) request('jabatan_id') === (string) $j->id ? 'selected' : '' }}>
                                    {{ $j->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Unit Kerja</label>
                        <select name="unit_kerja_id" class="form-select select2" data-placeholder="Semua">
                            <option value=""></option>
                            @foreach($unitKerja as $uk)
                                <option value="{{ $uk->getRouteKey() }}" {{ (string) request('unit_kerja_id') === (string) ($selectedUnitKerjaParam ?? '') || (string) request('unit_kerja_id') === (string) $uk->id ? 'selected' : '' }}>
                                    {{ $uk->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Agama</label>
                        <select name="agama" class="form-select">
                            <option value="">Semua</option>
                            <option value="Islam" {{ request('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen" {{ request('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                            <option value="Katolik" {{ request('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                            <option value="Hindu" {{ request('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ request('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                            <option value="Konghucu" {{ request('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> Filter
                        </button>
                        <a href="{{ route('laporan.pegawai') }}" class="btn btn-secondary">
                            <i class="fas fa-sync me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Hasil Laporan -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table me-2"></i>Hasil Laporan ({{ $pegawai->count() }} data)
            </h6>
            <div>
                <a href="{{ route('laporan.export', ['type' => 'pegawai'] + request()->all()) }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel me-1"></i> Export Excel
                </a>
                <a href="{{ route('laporan.print', ['type' => 'pegawai'] + request()->all()) }}" target="_blank" class="btn btn-secondary btn-sm">
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
                            <th>JK</th>
                            <th>Status</th>
                            <th>Golongan</th>
                            <th>Jabatan</th>
                            <th>Unit Kerja</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pegawai as $index => $p)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $p->nip }}</td>
                                <td>{{ $p->nama }}</td>
                                <td>{{ $p->jenis_kelamin == 'L' ? 'L' : 'P' }}</td>
                                <td>
                                    <span class="badge bg-{{ $p->status_pegawai == 'PNS' ? 'success' : ($p->status_pegawai == 'CPNS' ? 'info' : ($p->status_pegawai == 'PPPK' ? 'warning' : 'secondary')) }}">
                                        {{ $p->status_pegawai }}
                                    </span>
                                </td>
                                <td>{{ $p->golongan?->nama ?? '-' }}</td>
                                <td>{{ Str::limit($p->jabatan?->nama ?? '-', 30) }}</td>
                                <td>{{ Str::limit($p->unitKerja?->nama ?? '-', 30) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data</td>
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
        order: [[2, 'asc']]
    });
});
</script>
@endpush
