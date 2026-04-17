@extends('layouts.app')

@section('title', 'Ajukan Perjalanan Dinas')
@section('page-title', 'Ajukan Perjalanan Dinas')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-airplane me-2"></i>Form Perjalanan Dinas</div>
            <div class="card-body">
                <form action="{{ route('perjalanan-dinas.store') }}" method="POST">
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
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nomor Surat</label>
                                <input type="text" name="nomor_surat" class="form-control" value="{{ old('nomor_surat') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Surat</label>
                                <input type="date" name="tanggal_surat" class="form-control" value="{{ old('tanggal_surat', date('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tujuan <span class="text-danger">*</span></label>
                        <input type="text" name="tujuan" class="form-control @error('tujuan') is-invalid @enderror" value="{{ old('tujuan') }}" required>
                        @error('tujuan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Keperluan <span class="text-danger">*</span></label>
                        <textarea name="keperluan" class="form-control @error('keperluan') is-invalid @enderror" rows="3" required>{{ old('keperluan') }}</textarea>
                        @error('keperluan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Berangkat <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_berangkat" class="form-control @error('tanggal_berangkat') is-invalid @enderror" value="{{ old('tanggal_berangkat') }}" required>
                                @error('tanggal_berangkat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Kembali <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_kembali" class="form-control @error('tanggal_kembali') is-invalid @enderror" value="{{ old('tanggal_kembali') }}" required>
                                @error('tanggal_kembali')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Transportasi</label>
                                <select name="transportasi" class="form-select">
                                    <option value="">-- Pilih Transportasi --</option>
                                    @foreach(['Pesawat', 'Kereta Api', 'Bus', 'Mobil Dinas', 'Kendaraan Pribadi', 'Lainnya'] as $t)
                                    <option value="{{ $t }}" {{ old('transportasi') == $t ? 'selected' : '' }}>{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Biaya</label>
                                <input type="number" name="biaya" class="form-control" value="{{ old('biaya') }}" step="0.01">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Simpan
                        </button>
                        <a href="{{ route('perjalanan-dinas.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Informasi</div>
            <div class="card-body">
                <p class="text-muted">Setelah mengajukan perjalanan dinas, tunggu persetujuan dari Admin.</p>
                <hr>
                <p class="mb-1"><strong>Status:</strong></p>
                <p class="mb-1"><span class="badge bg-warning">Pending</span> - Menunggu persetujuan</p>
                <p class="mb-1"><span class="badge bg-success">Disetujui</span> - Sudah disetujui</p>
                <p class="mb-1"><span class="badge bg-danger">Ditolak</span> - Ditolak</p>
                <p class="mb-0"><span class="badge bg-info">Selesai</span> - Perjalanan selesai</p>
            </div>
        </div>
    </div>
</div>
@endsection
