@extends('layouts.app')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <span><i class="bi bi-people-fill me-2"></i>Daftar User</span>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus me-1"></i>Tambah User
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari username atau nama..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select">
                    <option value="">-- Semua Role --</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="sub_admin" {{ request('role') == 'sub_admin' ? 'selected' : '' }}>Sub Admin</option>
                    <option value="pegawai" {{ request('role') == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Cari</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Username</th>
                        <th>Nama</th>
                        <th>Pegawai</th>
                        <th>Role</th>
                        <th>Unit Kerja</th>
                        <th>Status</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $i => $user)
                    <tr>
                        <td>{{ $users->firstItem() + $i }}</td>
                        <td><strong>{{ $user->username }}</strong></td>
                        <td>{{ $user->nama }}</td>
                        <td>
                            @if($user->pegawai)
                            <span class="badge bg-light text-dark">{{ $user->pegawai->nip }}</span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($user->role == 'admin')
                            <span class="badge bg-danger">Admin</span>
                            @elseif($user->role == 'sub_admin')
                            <span class="badge bg-warning text-dark">Sub Admin</span>
                            @else
                            <span class="badge bg-primary">Pegawai</span>
                            @endif
                        </td>
                        <td>
                            @if($user->unitKerja)
                            <span class="badge bg-info">{{ $user->unitKerja->nama }}</span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}">
                                {{ $user->is_active ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('users.reset-password', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Reset password user ini?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-info text-white" title="Reset Password">
                                    <i class="bi bi-key"></i>
                                </button>
                            </form>
                            @if($user->id != auth()->id())
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $users->links() }}
    </div>
</div>
@endsection
