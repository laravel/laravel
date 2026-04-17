@extends('layouts.app')

@section('title', 'Tambah KGB')
@section('page-title', 'Tambah Kenaikan Gaji Berkala')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-cash-coin me-2"></i>Form KGB</div>
            <div class="card-body">
                <form action="{{ route('kgb.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Pegawai <span class="text-danger">*</span></label>
                        <select name="pegawai_id" class="form-select select2 @error('pegawai_id') is-invalid @enderror" data-placeholder="-- Pilih Pegawai --" required>
                            <option value=""></option>
                            @foreach($pegawai as $p)
                            <option value="{{ $p->id }}" {{ old('pegawai_id') == $p->id ? 'selected' : '' }}>
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
                                    <option value="{{ $g->id }}" {{ old('golongan_lama_id') == $g->id ? 'selected' : '' }}>{{ $g->kode }} - {{ $g->nama }}</option>
                                    @endforeach
                                </select>
                                @error('golongan_lama_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">TMT Golongan Lama <span class="text-danger">*</span></label>
                                <input type="date" name="tmt_golongan_lama" class="form-control @error('tmt_golongan_lama') is-invalid @enderror" value="{{ old('tmt_golongan_lama') }}" required>
                                @error('tmt_golongan_lama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">TMT KGB <span class="text-danger">*</span></label>
                                <input type="date" name="tmt_kgb" class="form-control @error('tmt_kgb') is-invalid @enderror" value="{{ old('tmt_kgb') }}" required>
                                @error('tmt_kgb')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">TMT KGB Berikutnya</label>
                                <input type="date" name="tmt_kgb_berikutnya" class="form-control" value="{{ old('tmt_kgb_berikutnya') }}">
                                <small class="text-muted">Akan dihitung otomatis +2 tahun jika kosong</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Masa Kerja (Tahun) <span class="text-danger">*</span></label>
                                <input type="number" name="masa_kerja_tahun" class="form-control @error('masa_kerja_tahun') is-invalid @enderror" value="{{ old('masa_kerja_tahun', 0) }}" min="0" required>
                                @error('masa_kerja_tahun')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Masa Kerja (Bulan) <span class="text-danger">*</span></label>
                                <input type="number" name="masa_kerja_bulan" class="form-control @error('masa_kerja_bulan') is-invalid @enderror" value="{{ old('masa_kerja_bulan', 0) }}" min="0" max="11" required>
                                @error('masa_kerja_bulan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Gaji Pokok Lama</label>
                                <input type="number" name="gaji_pokok_lama" class="form-control" value="{{ old('gaji_pokok_lama') }}" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Gaji Pokok Baru</label>
                                <input type="number" name="gaji_pokok_baru" class="form-control" value="{{ old('gaji_pokok_baru') }}" step="0.01">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nomor SK</label>
                                <input type="text" name="nomor_sk" class="form-control" value="{{ old('nomor_sk') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal SK</label>
                                <input type="date" name="tanggal_sk" class="form-control" value="{{ old('tanggal_sk') }}">
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
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Informasi KGB</div>
            <div class="card-body">
                <p class="text-muted mb-2">Kenaikan Gaji Berkala (KGB) diberikan kepada PNS yang telah mencapai masa kerja golongan tertentu.</p>
                <hr>
                <p class="mb-1"><strong>Ketentuan:</strong></p>
                <ul class="text-muted small">
                    <li>KGB diberikan setiap 2 tahun sekali</li>
                    <li>Masa kerja dihitung berdasarkan TMT golongan</li>
                    <li>Pegawai harus memiliki DP3/SKP minimal "Baik"</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
