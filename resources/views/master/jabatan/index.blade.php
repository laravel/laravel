@extends('layouts.app')

@section('title', 'Data Jabatan')
@section('page-title', 'Data Jabatan')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-briefcase me-2"></i>Daftar Jabatan</span>
        <a href="{{ route('jabatan.create') }}" class="btn btn-primary btn-sm">
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
                        <th>Eselon</th>
                        <th>Tunjangan</th>
                        <th>Keterangan</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jabatan as $i => $j)
                    <tr>
                        <td>{{ $jabatan->firstItem() + $i }}</td>
                        <td><strong>{{ $j->kode }}</strong></td>
                        <td>{{ $j->nama }}</td>
                        <td>{{ $j->eselon ?? '-' }}</td>
                        <td>Rp {{ number_format($j->tunjangan, 0, ',', '.') }}</td>
                        <td>{{ $j->keterangan ?? '-' }}</td>
                        <td>
                            <a href="{{ route('jabatan.edit', $j) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('jabatan.destroy', $j) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
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
                        <td colspan="7" class="text-center text-muted">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $jabatan->links() }}
    </div>
</div>
@endsection
