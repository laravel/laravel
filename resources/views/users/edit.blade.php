@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-person-gear me-2"></i>Form Edit User</div>
            <div class="card-body">
                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}" required>
                                @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $user->nama) }}" required>
                                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Password Baru</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                                <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Role <span class="text-danger">*</span></label>
                                <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="pegawai" {{ old('role', $user->role) == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                                    <option value="sub_admin" {{ old('role', $user->role) == 'sub_admin' ? 'selected' : '' }}>Sub Admin</option>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
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
                                    <option value="{{ $p->id }}" {{ old('pegawai_id', $user->pegawai_id) == $p->id ? 'selected' : '' }}>{{ $p->nip }} - {{ $p->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row" id="unit_kerja_row" style="{{ old('role', $user->role) == 'sub_admin' ? '' : 'display: none;' }}">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Unit Kerja <span class="text-danger">*</span></label>
                                <select name="unit_kerja_id" id="unit_kerja_id" class="form-select select2 @error('unit_kerja_id') is-invalid @enderror" data-placeholder="-- Pilih Unit Kerja --">
                                    <option value=""></option>
                                    @foreach($unitKerja as $uk)
                                    <option value="{{ $uk->id }}" {{ old('unit_kerja_id', $user->unit_kerja_id) == $uk->id ? 'selected' : '' }}>{{ $uk->nama }}</option>
                                    @endforeach
                                </select>
                                @error('unit_kerja_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-muted">Sub Admin hanya dapat mengakses pegawai dari unit kerja yang dipilih</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">User Aktif</label>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Update
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
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-person me-2"></i>Info User</div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td class="text-muted">Dibuat</td>
                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Terakhir Login</td>
                        <td>{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header"><i class="bi bi-key me-2"></i>Reset Password</div>
            <div class="card-body">
                <p class="text-muted small mb-3">Klik tombol di bawah untuk mereset password user menjadi password sementara acak.</p>
                <form action="{{ route('users.reset-password', $user) }}" method="POST" onsubmit="return confirm('Reset password user ini ke password sementara acak?')">
                    @csrf
                    <button type="submit" class="btn btn-warning w-100">
                        <i class="bi bi-key me-1"></i>Reset Password
                    </button>
                </form>
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
        }
    }
    
    roleSelect.addEventListener('change', toggleUnitKerja);
    toggleUnitKerja();
});
</script>
@endpush
@endsection
