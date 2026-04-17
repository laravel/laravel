@extends('layouts.app')

@section('title', 'Perjalanan Dinas')
@section('page-title', 'Perjalanan Dinas')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <span><i class="bi bi-airplane me-2"></i>Daftar Perjalanan Dinas</span>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('perjalanan-dinas.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus me-1"></i>Ajukan Perjalanan Dinas
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">-- Semua Status --</option>
                    @foreach(['pending', 'disetujui', 'ditolak', 'selesai'] as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="month" name="bulan" class="form-control" value="{{ request('bulan') }}">
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
                        <th>Nomor Surat</th>
                        @if(auth()->user()->role == 'admin')
                        <th>Pegawai</th>
                        @endif
                        <th>Tujuan</th>
                        <th>Keperluan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th width="130">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($perjalananDinas as $i => $pd)
                    <tr>
                        <td>{{ $perjalananDinas->firstItem() + $i }}</td>
                        <td><strong>{{ $pd->nomor_surat ?? '-' }}</strong></td>
                        @if(auth()->user()->role == 'admin')
                        <td>{{ $pd->pegawai->nama }}</td>
                        @endif
                        <td>{{ $pd->tujuan }}</td>
                        <td>{{ Str::limit($pd->keperluan, 30) }}</td>
                        <td>
                            {{ $pd->tanggal_berangkat->format('d/m/Y') }}
                            @if($pd->tanggal_kembali != $pd->tanggal_berangkat)
                            - {{ $pd->tanggal_kembali->format('d/m/Y') }}
                            @endif
                            <br><small class="text-muted">{{ $pd->lama_hari }} hari</small>
                        </td>
                        <td>
                            @php
                            $badges = [
                                'pending' => 'warning',
                                'disetujui' => 'success',
                                'ditolak' => 'danger',
                                'selesai' => 'info'
                            ];
                            @endphp
                            <span class="badge bg-{{ $badges[$pd->status] ?? 'secondary' }}">
                                {{ ucfirst($pd->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('perjalanan-dinas.show', $pd) }}" class="btn btn-sm btn-info text-white">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($pd->status == 'pending')
                            <a href="{{ route('perjalanan-dinas.edit', $pd) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('perjalanan-dinas.destroy', $pd) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->role == 'admin' ? 8 : 7 }}" class="text-center text-muted">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $perjalananDinas->links() }}
    </div>
</div>
@endsection
