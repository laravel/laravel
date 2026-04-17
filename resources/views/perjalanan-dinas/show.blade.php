@extends('layouts.app')

@section('title', 'Detail Perjalanan Dinas')
@section('page-title', 'Detail Perjalanan Dinas')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-airplane me-2"></i>Detail Perjalanan Dinas</span>
                    @php
                    $badges = [
                        'pending' => 'warning',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                        'selesai' => 'info'
                    ];
                    @endphp
                    <span class="badge bg-{{ $badges[$perjalananDinas->status] ?? 'secondary' }}">{{ ucfirst($perjalananDinas->status) }}</span>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%" class="text-muted">Nomor Surat</td>
                        <td>{{ $perjalananDinas->nomor_surat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal Surat</td>
                        <td>{{ $perjalananDinas->tanggal_surat ? $perjalananDinas->tanggal_surat->format('d/m/Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Pegawai</td>
                        <td>
                            <strong>{{ $perjalananDinas->pegawai->nama }}</strong><br>
                            <small class="text-muted">{{ $perjalananDinas->pegawai->nip }}</small>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tujuan</td>
                        <td><strong>{{ $perjalananDinas->tujuan }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Keperluan</td>
                        <td>{{ $perjalananDinas->keperluan }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal</td>
                        <td>
                            {{ $perjalananDinas->tanggal_berangkat->format('d/m/Y') }} - {{ $perjalananDinas->tanggal_kembali->format('d/m/Y') }}
                            <br><small class="text-muted">({{ $perjalananDinas->lama_hari }} hari)</small>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Transportasi</td>
                        <td>{{ $perjalananDinas->transportasi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Biaya</td>
                        <td>{{ $perjalananDinas->biaya ? 'Rp ' . number_format($perjalananDinas->biaya, 0, ',', '.') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Keterangan</td>
                        <td>{{ $perjalananDinas->keterangan ?? '-' }}</td>
                    </tr>
                </table>

                @if($perjalananDinas->status == 'ditolak' && $perjalananDinas->alasan_ditolak)
                <div class="alert alert-danger">
                    <strong><i class="bi bi-exclamation-triangle me-2"></i>Alasan Ditolak:</strong><br>
                    {{ $perjalananDinas->alasan_ditolak }}
                </div>
                @endif
            </div>
        </div>
        
        <div class="d-flex gap-2">
            @if($perjalananDinas->status == 'pending')
            <a href="{{ route('perjalanan-dinas.edit', $perjalananDinas) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
            @endif
            <a href="{{ route('perjalanan-dinas.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>
    
    <div class="col-md-4">
        @if(auth()->user()->role == 'admin')
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-gear me-2"></i>Aksi Admin</div>
            <div class="card-body">
                @if($perjalananDinas->status == 'pending')
                <form action="{{ route('perjalanan-dinas.approve', $perjalananDinas) }}" method="POST" class="mb-2">
                    @csrf
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-check-circle me-1"></i>Setujui
                    </button>
                </form>
                
                <form action="{{ route('perjalanan-dinas.reject', $perjalananDinas) }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <textarea name="alasan" class="form-control" rows="2" placeholder="Alasan penolakan..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-x-circle me-1"></i>Tolak
                    </button>
                </form>
                @elseif($perjalananDinas->status == 'disetujui')
                <form action="{{ route('perjalanan-dinas.selesai', $perjalananDinas) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-info w-100 text-white">
                        <i class="bi bi-flag me-1"></i>Tandai Selesai
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
                        <small class="text-muted">{{ $perjalananDinas->created_at->format('d/m/Y H:i') }}</small><br>
                        Pengajuan dibuat
                    </li>
                    @if($perjalananDinas->approved_at)
                    <li class="mb-2">
                        <small class="text-muted">{{ $perjalananDinas->approved_at->format('d/m/Y H:i') }}</small><br>
                        {{ $perjalananDinas->status == 'ditolak' ? 'Ditolak' : 'Disetujui' }} oleh {{ $perjalananDinas->approvedBy->nama ?? 'Admin' }}
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
