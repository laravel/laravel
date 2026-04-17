@extends('layouts.app')

@section('title', 'Data Kabupaten/Kota')
@section('page-title', 'Data Kabupaten/Kota')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-geo-alt me-2"></i>Daftar Kabupaten/Kota</span>
        <a href="{{ route('kabupaten-kota.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus me-1"></i>Tambah
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Tipe</th>
                        <th>Provinsi</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kabupatenKota as $i => $kk)
                    <tr>
                        <td>{{ $kabupatenKota->firstItem() + $i }}</td>
                        <td><strong>{{ $kk->kode }}</strong></td>
                        <td>{{ $kk->nama }}</td>
                        <td>
                            <span class="badge bg-{{ $kk->tipe == 'Kota' ? 'info' : 'secondary' }}">
                                {{ $kk->tipe }}
                            </span>
                        </td>
                        <td>{{ $kk->provinsi ?? '-' }}</td>
                        <td>
                            <a href="{{ route('kabupaten-kota.edit', $kk) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('kabupaten-kota.destroy', $kk) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
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
                        <td colspan="6" class="text-center text-muted">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $kabupatenKota->links() }}
    </div>
</div>
@endsection
