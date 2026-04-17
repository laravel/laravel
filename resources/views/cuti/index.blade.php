@extends('layouts.app')

@section('title', 'Cuti')
@section('page-title', 'Cuti')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <span><i class="bi bi-calendar-check me-2"></i>Daftar Pengajuan Cuti</span>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('cuti.saldo') }}" class="btn btn-info btn-sm text-white me-2">
                    <i class="bi bi-wallet me-1"></i>Saldo Cuti
                </a>
                <a href="{{ route('cuti.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus me-1"></i>Ajukan Cuti
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <select name="jenis_cuti_id" class="form-select">
                    <option value="">-- Semua Jenis --</option>
                    @foreach($jenisCuti as $jc)
                    <option value="{{ $jc->getRouteKey() }}" {{ (string) request('jenis_cuti_id') === (string) ($selectedJenisCutiParam ?? '') || (string) request('jenis_cuti_id') === (string) $jc->id ? 'selected' : '' }}>{{ $jc->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">-- Semua Status --</option>
                    @foreach(['pending', 'disetujui', 'ditolak'] as $status)
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
                        @if(auth()->user()->role == 'admin')
                        <th>Pegawai</th>
                        @endif
                        <th>Jenis Cuti</th>
                        <th>Tanggal</th>
                        <th>Lama</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th width="130">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cuti as $i => $c)
                    <tr>
                        <td>{{ $cuti->firstItem() + $i }}</td>
                        @if(auth()->user()->role == 'admin')
                        <td>{{ $c->pegawai->nama }}</td>
                        @endif
                        <td>{{ $c->jenisCuti->nama }}</td>
                        <td>
                            {{ $c->tanggal_mulai->format('d/m/Y') }}
                            @if($c->tanggal_selesai != $c->tanggal_mulai)
                            - {{ $c->tanggal_selesai->format('d/m/Y') }}
                            @endif
                        </td>
                        <td>{{ $c->jumlah_hari }} hari</td>
                        <td>{{ Str::limit($c->alasan, 30) }}</td>
                        <td>
                            @php
                            $badges = [
                                'pending' => 'warning',
                                'disetujui' => 'success',
                                'ditolak' => 'danger'
                            ];
                            @endphp
                            <span class="badge bg-{{ $badges[$c->status] ?? 'secondary' }}">
                                {{ ucfirst($c->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('cuti.show', $c) }}" class="btn btn-sm btn-info text-white">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($c->status == 'pending')
                            <a href="{{ route('cuti.edit', $c) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('cuti.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
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
        {{ $cuti->links() }}
    </div>
</div>
@endsection
