@extends('layouts.app')

@section('title', 'Kenaikan Gaji Berkala')
@section('page-title', 'Kenaikan Gaji Berkala (KGB)')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <span><i class="bi bi-cash-coin me-2"></i>Daftar KGB</span>
            </div>
            @if(auth()->user()->role == 'admin')
            <div class="col-md-6 text-end">
                <a href="{{ route('kgb.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus me-1"></i>Tambah KGB
                </a>
            </div>
            @endif
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Cari NIP atau Nama..." value="{{ request('search') }}">
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
                <input type="number" name="tahun" class="form-control" placeholder="Tahun" value="{{ request('tahun') }}">
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
                        <th>Golongan Lama</th>
                        <th>TMT KGB</th>
                        <th>Masa Kerja</th>
                        <th>Status</th>
                        <th width="130">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kgb as $i => $k)
                    <tr>
                        <td>{{ $kgb->firstItem() + $i }}</td>
                        <td><strong>{{ $k->pegawai->nip }}</strong></td>
                        <td>{{ $k->pegawai->nama }}</td>
                        <td>{{ $k->golonganLama->nama ?? '-' }}</td>
                        <td>{{ $k->tmt_kgb->format('d/m/Y') }}</td>
                        <td>{{ $k->masa_kerja_tahun }} th {{ $k->masa_kerja_bulan }} bl</td>
                        <td>
                            @php
                            $badges = [
                                'pending' => 'warning',
                                'disetujui' => 'success',
                                'ditolak' => 'danger'
                            ];
                            @endphp
                            <span class="badge bg-{{ $badges[$k->status] ?? 'secondary' }}">
                                {{ ucfirst($k->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('kgb.show', $k) }}" class="btn btn-sm btn-info text-white">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if(auth()->user()->role == 'admin' && $k->status == 'pending')
                            <a href="{{ route('kgb.edit', $k) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('kgb.destroy', $k) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
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
                        <td colspan="8" class="text-center text-muted">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $kgb->links() }}
    </div>
</div>
@endsection
