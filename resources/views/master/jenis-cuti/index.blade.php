@extends('layouts.app')

@section('title', 'Data Jenis Cuti')
@section('page-title', 'Data Jenis Cuti')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-calendar-check me-2"></i>Daftar Jenis Cuti</span>
        <a href="{{ route('jenis-cuti.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus me-1"></i>Tambah
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Nama</th>
                        <th>Max Hari</th>
                        <th>Keterangan</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jenisCuti as $i => $jc)
                    <tr>
                        <td>{{ $jenisCuti->firstItem() + $i }}</td>
                        <td><strong>{{ $jc->nama }}</strong></td>
                        <td>{{ $jc->max_hari }} hari</td>
                        <td>{{ $jc->keterangan ?? '-' }}</td>
                        <td>
                            <a href="{{ route('jenis-cuti.edit', $jc) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('jenis-cuti.destroy', $jc) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
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
                        <td colspan="5" class="text-center text-muted">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $jenisCuti->links() }}
    </div>
</div>
@endsection
