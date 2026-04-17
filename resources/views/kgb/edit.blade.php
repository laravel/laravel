@extends('layouts.app')

@section('title', 'Edit KGB')
@section('page-title', 'Edit Kenaikan Gaji Berkala')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-cash-coin me-2"></i>Edit KGB</div>
            <div class="card-body">
                <form action="{{ route('kgb.update', $kgb) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Pegawai <span class="text-danger">*</span></label>
                        <select name="pegawai_id" class="form-select select2 @error('pegawai_id') is-invalid @enderror" data-placeholder="-- Pilih Pegawai --" required>
                            <option value=""></option>
                            @foreach($pegawai as $p)
                            <option value="{{ $p->id }}" {{ old('pegawai_id', $kgb->pegawai_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->nip }} - {{ $p->nama }} ({{ $p->golongan->nama ?? 'Tanpa Golongan' }})
                            </option>
                            @endforeach
                        </select>
                        @error('pegawai_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Golongan Lama <span class="text-danger">*</span></label>
                                <select name="golongan_lama_id" class="form-select select2 @error('golongan_lama_id') is-invalid @enderror" data-placeholder="-- Pilih Golongan --" required>
                                    <option value=""></option>
                                    @foreach($golongan as $g)
                                    <option value="{{ $g->id }}" {{ old('golongan_lama_id', $kgb->golongan_lama_id) == $g->id ? 'selected' : '' }}>{{ $g->kode }} - {{ $g->nama }}</option>
                                    @endforeach
                                </select>
                                @error('golongan_lama_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">TMT Golongan Lama <span class="text-danger">*</span></label>
                                <input type="date" name="tmt_golongan_lama" class="form-control @error('tmt_golongan_lama') is-invalid @enderror" value="{{ old('tmt_golongan_lama', $kgb->tmt_golongan_lama->format('Y-m-d')) }}" required>
                                @error('tmt_golongan_lama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">TMT KGB <span class="text-danger">*</span></label>
                                <input type="date" name="tmt_kgb" class="form-control @error('tmt_kgb') is-invalid @enderror" value="{{ old('tmt_kgb', $kgb->tmt_kgb->format('Y-m-d')) }}" required>
                                @error('tmt_kgb')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">TMT KGB Berikutnya</label>
                                <input type="date" name="tmt_kgb_berikutnya" class="form-control" value="{{ old('tmt_kgb_berikutnya', $kgb->tmt_kgb_berikutnya?->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Masa Kerja (Tahun) <span class="text-danger">*</span></label>
                                <input type="number" name="masa_kerja_tahun" class="form-control @error('masa_kerja_tahun') is-invalid @enderror" value="{{ old('masa_kerja_tahun', $kgb->masa_kerja_tahun) }}" min="0" required>
                                @error('masa_kerja_tahun')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Masa Kerja (Bulan) <span class="text-danger">*</span></label>
                                <input type="number" name="masa_kerja_bulan" class="form-control @error('masa_kerja_bulan') is-invalid @enderror" value="{{ old('masa_kerja_bulan', $kgb->masa_kerja_bulan) }}" min="0" max="11" required>
                                @error('masa_kerja_bulan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Gaji Pokok Lama</label>
                                <input type="number" name="gaji_pokok_lama" class="form-control" value="{{ old('gaji_pokok_lama', $kgb->gaji_pokok_lama) }}" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Gaji Pokok Baru</label>
                                <input type="number" name="gaji_pokok_baru" class="form-control" value="{{ old('gaji_pokok_baru', $kgb->gaji_pokok_baru) }}" step="0.01">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nomor SK</label>
                                <input type="text" name="nomor_sk" class="form-control" value="{{ old('nomor_sk', $kgb->nomor_sk) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal SK</label>
                                <input type="date" name="tanggal_sk" class="form-control" value="{{ old('tanggal_sk', $kgb->tanggal_sk?->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $kgb->keterangan) }}</textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Update
                        </button>
                        <a href="{{ route('kgb.index') }}" class="btn btn-secondary">
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
                <p>Status saat ini: <span class="badge bg-{{ $badges[$kgb->status] ?? 'secondary' }}">{{ ucfirst($kgb->status) }}</span></p>
                <hr>
                <small class="text-muted">Anda hanya dapat mengedit KGB yang masih berstatus Pending.</small>
            </div>
        </div>
    </div>
</div>
@endsection
