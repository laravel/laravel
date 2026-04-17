@extends('layouts.app')

@section('title', 'Data Pegawai')
@section('page-title', 'Data Pegawai')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <span><i class="bi bi-people me-2"></i>Daftar Pegawai</span>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('pegawai.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus me-1"></i>Tambah Pegawai
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari NIP atau Nama..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="unit_kerja_id" class="form-select select2" data-placeholder="-- Semua Unit Kerja --">
                    <option value=""></option>
                    @foreach($unitKerja as $uk)
                    <option value="{{ $uk->getRouteKey() }}" {{ (string) request('unit_kerja_id') === (string) ($selectedUnitKerjaParam ?? '') || (string) request('unit_kerja_id') === (string) $uk->id ? 'selected' : '' }}>{{ $uk->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Cari</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Golongan</th>
                        <th>Jabatan</th>
                        <th>Unit Kerja</th>
                        <th>Status</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pegawai as $i => $p)
                    <tr>
                        <td>{{ $pegawai->firstItem() + $i }}</td>
                        <td><strong>{{ $p->nip }}</strong></td>
                        <td>{{ $p->nama }}</td>
                        <td>{{ $p->golongan->nama ?? '-' }}</td>
                        <td>{{ $p->jabatan->nama ?? '-' }}</td>
                        <td>{{ $p->unitKerja->nama ?? '-' }}</td>
                        <td>
                            <form action="{{ route('pegawai.toggle-status', $p) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-{{ $p->is_active ? 'success' : 'secondary' }}" 
                                    onclick="return confirm('Ubah status pegawai menjadi {{ $p->is_active ? 'Non-Aktif' : 'Aktif' }}?')">
                                    <i class="bi bi-{{ $p->is_active ? 'check-circle' : 'x-circle' }} me-1"></i>
                                    {{ $p->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <a href="{{ route('pegawai.show', $p) }}" class="btn btn-sm btn-info text-white">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('pegawai.edit', $p) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('pegawai.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">Tidak ada data pegawai</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $pegawai->links() }}
    </div>
</div>
@endsection
