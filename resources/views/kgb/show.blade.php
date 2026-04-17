@extends('layouts.app')

@section('title', 'Detail KGB')
@section('page-title', 'Detail Kenaikan Gaji Berkala')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-cash-coin me-2"></i>Detail KGB</span>
                    @php
                    $badges = [
                        'pending' => 'warning',
                        'disetujui' => 'success',
                        'ditolak' => 'danger'
                    ];
                    @endphp
                    <span class="badge bg-{{ $badges[$kgb->status] ?? 'secondary' }}">{{ ucfirst($kgb->status) }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Data Pegawai</h6>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="40%" class="text-muted">NIP</td>
                                <td><strong>{{ $kgb->pegawai->nip }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Nama</td>
                                <td>{{ $kgb->pegawai->nama }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Unit Kerja</td>
                                <td>{{ $kgb->pegawai->unitKerja->nama ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Data KGB</h6>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="40%" class="text-muted">Golongan Lama</td>
                                <td>{{ $kgb->golonganLama->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">TMT Golongan Lama</td>
                                <td>{{ $kgb->tmt_golongan_lama->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Masa Kerja</td>
                                <td><strong>{{ $kgb->masa_kerja_tahun }} tahun {{ $kgb->masa_kerja_bulan }} bulan</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Tanggal</h6>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="40%" class="text-muted">TMT KGB</td>
                                <td><strong>{{ $kgb->tmt_kgb->format('d/m/Y') }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">TMT KGB Berikutnya</td>
                                <td>{{ $kgb->tmt_kgb_berikutnya ? $kgb->tmt_kgb_berikutnya->format('d/m/Y') : '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Gaji Pokok</h6>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="40%" class="text-muted">Gaji Pokok Lama</td>
                                <td>{{ $kgb->gaji_pokok_lama ? 'Rp ' . number_format($kgb->gaji_pokok_lama, 0, ',', '.') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Gaji Pokok Baru</td>
                                <td><strong>{{ $kgb->gaji_pokok_baru ? 'Rp ' . number_format($kgb->gaji_pokok_baru, 0, ',', '.') : '-' }}</strong></td>
                            </tr>
                            @if($kgb->gaji_pokok_lama && $kgb->gaji_pokok_baru)
                            <tr>
                                <td class="text-muted">Kenaikan</td>
                                <td class="text-success">+ Rp {{ number_format($kgb->gaji_pokok_baru - $kgb->gaji_pokok_lama, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="text-muted mb-3">SK KGB</h6>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="20%" class="text-muted">Nomor SK</td>
                                <td>{{ $kgb->nomor_sk ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tanggal SK</td>
                                <td>{{ $kgb->tanggal_sk ? $kgb->tanggal_sk->format('d/m/Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Keterangan</td>
                                <td>{{ $kgb->keterangan ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($kgb->status == 'ditolak' && $kgb->alasan_ditolak)
                <div class="alert alert-danger">
                    <strong><i class="bi bi-exclamation-triangle me-2"></i>Alasan Ditolak:</strong><br>
                    {{ $kgb->alasan_ditolak }}
                </div>
                @endif
            </div>
        </div>
        
        <div class="d-flex gap-2">
            @if(auth()->user()->role == 'admin' && $kgb->status == 'pending')
            <a href="{{ route('kgb.edit', $kgb) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
            @endif
            <a href="{{ route('kgb.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>
    
    <div class="col-md-4">
        @if(auth()->user()->role == 'admin')
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-gear me-2"></i>Aksi Admin</div>
            <div class="card-body">
                @if($kgb->status == 'pending')
                <form action="{{ route('kgb.approve', $kgb) }}" method="POST" class="mb-2">
                    @csrf
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-check-circle me-1"></i>Setujui
                    </button>
                </form>
                
                <form action="{{ route('kgb.reject', $kgb) }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <textarea name="alasan" class="form-control" rows="2" placeholder="Alasan penolakan..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-x-circle me-1"></i>Tolak
                    </button>
                </form>
                @else
                <p class="text-muted mb-0">Tidak ada aksi yang tersedia.</p>
                @endif
            </div>
        </div>
        @endif
        
        <div class="card">
            <div class="card-header"><i class="bi bi-clock-history me-2"></i>Riwayat</div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <small class="text-muted">{{ $kgb->created_at->format('d/m/Y H:i') }}</small><br>
                        Data KGB dibuat
                    </li>
                    @if($kgb->approved_at)
                    <li class="mb-2">
                        <small class="text-muted">{{ $kgb->approved_at->format('d/m/Y H:i') }}</small><br>
                        {{ $kgb->status == 'ditolak' ? 'Ditolak' : 'Disetujui' }} oleh {{ $kgb->approvedBy->nama ?? 'Admin' }}
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
