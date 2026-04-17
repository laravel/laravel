@extends('layouts.app')

@section('title', 'Tambah User')
@section('page-title', 'Tambah User')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-person-plus me-2"></i>Form Tambah User</div>
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" required>
                                @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Role <span class="text-danger">*</span></label>
                                <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="pegawai" {{ old('role') == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                                    <option value="sub_admin" {{ old('role') == 'sub_admin' ? 'selected' : '' }}>Sub Admin</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Link ke Pegawai</label>
                                <select name="pegawai_id" class="form-select select2" data-placeholder="-- Tidak Ada --">
                                    <option value=""></option>
                                    @foreach($pegawai as $p)
                                    <option value="{{ $p->id }}" {{ old('pegawai_id') == $p->id ? 'selected' : '' }}>{{ $p->nip }} - {{ $p->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row" id="unit_kerja_row" style="{{ old('role') == 'sub_admin' ? '' : 'display: none;' }}">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Unit Kerja <span class="text-danger">*</span></label>
                                <select name="unit_kerja_id" id="unit_kerja_id" class="form-select select2 @error('unit_kerja_id') is-invalid @enderror" data-placeholder="-- Pilih Unit Kerja --">
                                    <option value=""></option>
                                    @foreach($unitKerja as $uk)
                                    <option value="{{ $uk->id }}" {{ old('unit_kerja_id') == $uk->id ? 'selected' : '' }}>{{ $uk->nama }}</option>
                                    @endforeach
                                </select>
                                @error('unit_kerja_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-muted">Sub Admin hanya dapat mengakses pegawai dari unit kerja yang dipilih</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">User Aktif</label>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Simpan
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
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
                <p class="text-muted mb-2">Tambahkan user baru untuk mengakses sistem.</p>
                <hr>
                <p class="mb-1"><strong>Role:</strong></p>
                <p class="mb-1"><span class="badge bg-danger">Admin</span> - Akses penuh ke semua fitur</p>
                <p class="mb-1"><span class="badge bg-warning text-dark">Sub Admin</span> - Akses pegawai per unit kerja</p>
                <p class="mb-0"><span class="badge bg-primary">Pegawai</span> - Akses terbatas untuk fitur pegawai</p>
                <hr>
                <p class="text-muted small mb-0">Jika user adalah pegawai, hubungkan ke data pegawai yang sesuai.</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const unitKerjaRow = document.getElementById('unit_kerja_row');
    const unitKerjaSelect = document.getElementById('unit_kerja_id');
    
    function toggleUnitKerja() {
        if (roleSelect.value === 'sub_admin') {
            unitKerjaRow.style.display = '';
            unitKerjaSelect.required = true;
        } else {
            unitKerjaRow.style.display = 'none';
            unitKerjaSelect.required = false;
            unitKerjaSelect.value = '';
        }
    }
    
    roleSelect.addEventListener('change', toggleUnitKerja);
    toggleUnitKerja();
});
</script>
@endpush
@endsection
