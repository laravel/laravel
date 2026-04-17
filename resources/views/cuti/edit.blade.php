@extends('layouts.app')

@section('title', 'Edit Cuti')
@section('page-title', 'Edit Cuti')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-calendar-check me-2"></i>Edit Pengajuan Cuti</div>
            <div class="card-body">
                <form action="{{ route('cuti.update', $cuti) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    @if(auth()->user()->role == 'admin')
                    <div class="mb-3">
                        <label class="form-label">Pegawai <span class="text-danger">*</span></label>
                        <select name="pegawai_id" class="form-select select2 @error('pegawai_id') is-invalid @enderror" data-placeholder="-- Pilih Pegawai --" required>
                            <option value=""></option>
                            @foreach($pegawai as $p)
                            <option value="{{ $p->id }}" {{ old('pegawai_id', $cuti->pegawai_id) == $p->id ? 'selected' : '' }}>{{ $p->nip }} - {{ $p->nama }}</option>
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
                            <option value="{{ $jc->id }}" {{ old('jenis_cuti_id', $cuti->jenis_cuti_id) == $jc->id ? 'selected' : '' }}>
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
                                <input type="date" name="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" value="{{ old('tanggal_mulai', $cuti->tanggal_mulai->format('Y-m-d')) }}" required>
                                @error('tanggal_mulai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" value="{{ old('tanggal_selesai', $cuti->tanggal_selesai->format('Y-m-d')) }}" required>
                                @error('tanggal_selesai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alasan <span class="text-danger">*</span></label>
                        <textarea name="alasan" class="form-control @error('alasan') is-invalid @enderror" rows="3" required>{{ old('alasan', $cuti->alasan) }}</textarea>
                        @error('alasan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alamat Selama Cuti</label>
                        <textarea name="alamat_cuti" class="form-control" rows="2">{{ old('alamat_cuti', $cuti->alamat_cuti) }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $cuti->keterangan) }}</textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Update
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
        <div class="card">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Status</div>
            <div class="card-body">
                @php
                $badges = [
                    'pending' => 'warning',
                    'disetujui' => 'success',
                    'ditolak' => 'danger'
                ];
                @endphp
                <p>Status saat ini: <span class="badge bg-{{ $badges[$cuti->status] ?? 'secondary' }}">{{ ucfirst($cuti->status) }}</span></p>
                <hr>
                <small class="text-muted">Anda hanya dapat mengedit cuti yang masih berstatus Pending.</small>
            </div>
        </div>
    </div>
</div>
@endsection
