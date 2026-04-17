@extends('layouts.app')

@section('title', 'Detail Cuti')
@section('page-title', 'Detail Cuti')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-calendar-check me-2"></i>Detail Pengajuan Cuti</span>
                    @php
                    $badges = [
                        'pending' => 'warning',
                        'disetujui' => 'success',
                        'ditolak' => 'danger'
                    ];
                    @endphp
                    <span class="badge bg-{{ $badges[$cuti->status] ?? 'secondary' }}">{{ ucfirst($cuti->status) }}</span>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%" class="text-muted">Pegawai</td>
                        <td>
                            <strong>{{ $cuti->pegawai->nama }}</strong><br>
                            <small class="text-muted">{{ $cuti->pegawai->nip }}</small>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jenis Cuti</td>
                        <td><span class="badge bg-primary">{{ $cuti->jenisCuti->nama }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal</td>
                        <td>
                            {{ $cuti->tanggal_mulai->format('d/m/Y') }} - {{ $cuti->tanggal_selesai->format('d/m/Y') }}
                            <br><small class="text-muted">({{ $cuti->jumlah_hari }} hari)</small>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Alasan</td>
                        <td>{{ $cuti->alasan }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Alamat Selama Cuti</td>
                        <td>{{ $cuti->alamat_cuti ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Keterangan</td>
                        <td>{{ $cuti->keterangan ?? '-' }}</td>
                    </tr>
                </table>

                @if($cuti->status == 'ditolak' && $cuti->alasan_ditolak)
                <div class="alert alert-danger">
                    <strong><i class="bi bi-exclamation-triangle me-2"></i>Alasan Ditolak:</strong><br>
                    {{ $cuti->alasan_ditolak }}
                </div>
                @endif
            </div>
        </div>
        
        <div class="d-flex gap-2">
            @if($cuti->status == 'pending')
            <a href="{{ route('cuti.edit', $cuti) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
            @endif
            <a href="{{ route('cuti.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>
    
    <div class="col-md-4">
        @if(auth()->user()->role == 'admin')
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-gear me-2"></i>Aksi Admin</div>
            <div class="card-body">
                @if($cuti->status == 'pending')
                <form action="{{ route('cuti.approve', $cuti) }}" method="POST" class="mb-2">
                    @csrf
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-check-circle me-1"></i>Setujui
                    </button>
                </form>
                
                <form action="{{ route('cuti.reject', $cuti) }}" method="POST">
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
                        <small class="text-muted">{{ $cuti->created_at->format('d/m/Y H:i') }}</small><br>
                        Pengajuan dibuat
                    </li>
                    @if($cuti->approved_at)
                    <li class="mb-2">
                        <small class="text-muted">{{ $cuti->approved_at->format('d/m/Y H:i') }}</small><br>
                        {{ $cuti->status == 'ditolak' ? 'Ditolak' : 'Disetujui' }} oleh {{ $cuti->approvedBy->nama ?? 'Admin' }}
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
