@extends('layouts.app')

@section('title', 'Data Unit Kerja')
@section('page-title', 'Data Unit Kerja')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-diagram-3 me-2"></i>Daftar Unit Kerja</span>
        <a href="{{ route('unit-kerja.create') }}" class="btn btn-primary btn-sm">
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
                        <th>Alamat</th>
                        <th>Telepon</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($unitKerja as $i => $uk)
                    <tr>
                        <td>{{ $unitKerja->firstItem() + $i }}</td>
                        <td><strong>{{ $uk->kode }}</strong></td>
                        <td>{{ $uk->nama }}</td>
                        <td>{{ $uk->alamat ?? '-' }}</td>
                        <td>{{ $uk->telepon ?? '-' }}</td>
                        <td>
                            <a href="{{ route('unit-kerja.edit', $uk) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('unit-kerja.destroy', $uk) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
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
        {{ $unitKerja->links() }}
    </div>
</div>
@endsection
