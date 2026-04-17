@extends('layouts.app')

@section('title', 'Data Golongan')
@section('page-title', 'Data Golongan')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-layers me-2"></i>Daftar Golongan</span>
        <a href="{{ route('golongan.create') }}" class="btn btn-primary btn-sm">
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
                        <th>Gaji Pokok</th>
                        <th>Keterangan</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($golongan as $i => $g)
                    <tr>
                        <td>{{ $golongan->firstItem() + $i }}</td>
                        <td><strong>{{ $g->kode }}</strong></td>
                        <td>{{ $g->nama }}</td>
                        <td>Rp {{ number_format($g->gaji_pokok, 0, ',', '.') }}</td>
                        <td>{{ $g->keterangan ?? '-' }}</td>
                        <td>
                            <a href="{{ route('golongan.edit', $g) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('golongan.destroy', $g) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
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
        {{ $golongan->links() }}
    </div>
</div>
@endsection
