@extends('layouts.app')

@section('title', 'Saldo Cuti')
@section('page-title', 'Saldo Cuti')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <span><i class="bi bi-wallet me-2"></i>Saldo Cuti</span>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('cuti.index') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar Cuti
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if(auth()->user()->role == 'admin')
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari NIP atau Nama..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <input type="number" name="tahun" class="form-control" placeholder="Tahun" value="{{ request('tahun', date('Y')) }}">
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
                        @foreach($jenisCuti as $jc)
                        <th class="text-center">{{ $jc->nama }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($saldoCuti as $i => $pegawai)
                    <tr>
                        <td>{{ $saldoCuti->firstItem() + $i }}</td>
                        <td><strong>{{ $pegawai->nip }}</strong></td>
                        <td>{{ $pegawai->nama }}</td>
                        @foreach($jenisCuti as $jc)
                        @php
                        $saldo = $pegawai->saldoCuti->where('jenis_cuti_id', $jc->id)->where('tahun', request('tahun', date('Y')))->first();
                        @endphp
                        <td class="text-center">
                            @if($saldo)
                            <span class="badge bg-{{ $saldo->sisa_cuti > 0 ? 'success' : 'secondary' }}">
                                {{ $saldo->sisa_cuti }} / {{ $saldo->jatah_cuti }}
                            </span>
                            @else
                            <span class="badge bg-light text-dark">-</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ 3 + count($jenisCuti) }}" class="text-center text-muted">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $saldoCuti->links() }}
        @else
        {{-- View untuk pegawai --}}
        <div class="row">
            @forelse($saldoCuti as $saldo)
            <div class="col-md-4 mb-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $saldo->jenisCuti->nama }}</h5>
                        <div class="d-flex justify-content-center align-items-center my-3">
                            <div class="display-4 text-primary">{{ $saldo->sisa_cuti }}</div>
                            <div class="ms-2 text-muted">/ {{ $saldo->jatah_cuti }}</div>
                        </div>
                        <p class="text-muted mb-0">Sisa cuti dari jatah {{ $saldo->jatah_cuti }} hari</p>
                    </div>
                    <div class="card-footer bg-transparent">
                        <small class="text-muted">Tahun {{ $saldo->tahun }}</small>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>Belum ada saldo cuti yang tercatat untuk Anda.
                </div>
            </div>
            @endforelse
        </div>
        
        @if(count($saldoCuti) > 0)
        <hr>
        <h5 class="mb-3">Riwayat Penggunaan Cuti Tahun {{ date('Y') }}</h5>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Jenis</th>
                        <th>Tanggal</th>
                        <th>Lama</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatCuti ?? [] as $c)
                    <tr>
                        <td>{{ $c->jenisCuti->nama }}</td>
                        <td>{{ $c->tanggal_mulai->format('d/m/Y') }} - {{ $c->tanggal_selesai->format('d/m/Y') }}</td>
                        <td>{{ $c->jumlah_hari }} hari</td>
                        <td>
                            @php
                            $badges = ['pending' => 'warning', 'disetujui' => 'success', 'ditolak' => 'danger'];
                            @endphp
                            <span class="badge bg-{{ $badges[$c->status] ?? 'secondary' }}">{{ ucfirst($c->status) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-muted text-center">Belum ada riwayat cuti</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
