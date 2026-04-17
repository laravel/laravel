@extends('layouts.app')

@section('title', 'Ajukan Cuti')
@section('page-title', 'Ajukan Cuti')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-calendar-check me-2"></i>Form Pengajuan Cuti</div>
            <div class="card-body">
                <form action="{{ route('cuti.store') }}" method="POST">
                    @csrf
                    
                    @if(auth()->user()->role == 'admin')
                    <div class="mb-3">
                        <label class="form-label">Pegawai <span class="text-danger">*</span></label>
                        <select name="pegawai_id" class="form-select select2 @error('pegawai_id') is-invalid @enderror" data-placeholder="-- Pilih Pegawai --" required>
                            <option value=""></option>
                            @foreach($pegawai as $p)
                            <option value="{{ $p->id }}" {{ old('pegawai_id') == $p->id ? 'selected' : '' }}>{{ $p->nip }} - {{ $p->nama }}</option>
                            @endforeach
                        </select>
                        @error('pegawai_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <label class="form-label">Jenis Cuti <span class="text-danger">*</span></label>
                        <select name="jenis_cuti_id" class="form-select @error('jenis_cuti_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis Cuti --</option>
                            @foreach($jenisCuti as $jc)
                            <option value="{{ $jc->id }}" {{ old('jenis_cuti_id') == $jc->id ? 'selected' : '' }} data-max="{{ $jc->max_hari }}">
                                {{ $jc->nama }} (max {{ $jc->max_hari ?? 'tidak terbatas' }} hari)
                            </option>
                            @endforeach
                        </select>
                        @error('jenis_cuti_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" value="{{ old('tanggal_mulai') }}" required>
                                @error('tanggal_mulai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" value="{{ old('tanggal_selesai') }}" required>
                                @error('tanggal_selesai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alasan <span class="text-danger">*</span></label>
                        <textarea name="alasan" class="form-control @error('alasan') is-invalid @enderror" rows="3" required>{{ old('alasan') }}</textarea>
                        @error('alasan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alamat Selama Cuti</label>
                        <textarea name="alamat_cuti" class="form-control" rows="2">{{ old('alamat_cuti') }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Ajukan Cuti
                        </button>
                        <a href="{{ route('cuti.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        @if(auth()->user()->role == 'pegawai' && auth()->user()->pegawai)
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-wallet me-2"></i>Saldo Cuti Anda</div>
            <div class="card-body">
                @forelse($saldoCuti ?? [] as $saldo)
                <div class="d-flex justify-content-between mb-2">
                    <span>{{ $saldo->jenisCuti->nama }}</span>
                    <strong>{{ $saldo->sisa_cuti }} hari</strong>
                </div>
                @empty
                <p class="text-muted mb-0">Belum ada saldo cuti</p>
                @endforelse
            </div>
        </div>
        @endif
        
        <div class="card">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Informasi</div>
            <div class="card-body">
                <p class="text-muted mb-2">Pastikan saldo cuti Anda mencukupi sebelum mengajukan.</p>
                <hr>
                <p class="mb-1"><strong>Status:</strong></p>
                <p class="mb-1"><span class="badge bg-warning">Pending</span> - Menunggu persetujuan</p>
                <p class="mb-1"><span class="badge bg-success">Disetujui</span> - Sudah disetujui</p>
                <p class="mb-0"><span class="badge bg-danger">Ditolak</span> - Ditolak</p>
            </div>
        </div>
    </div>
</div>
@endsection
