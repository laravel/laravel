@extends('layouts.app')

@section('title', 'Detail Pegawai - ' . $jabatan->nama)
@section('page-title', 'Detail Pegawai per Jabatan')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-people me-2"></i>
            Pegawai Jabatan: <strong>{{ $jabatan->nama }}</strong> 
            <span class="text-muted">- {{ $unitKerja->nama }}</span>
        </span>
        <a href="{{ route('peta-jabatan.index', ['unit_kerja_id' => $unitKerja->getRouteKey()]) }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>
    <div class="card-body">
        <!-- Summary -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="border rounded p-3 text-center">
                    <h6 class="text-muted mb-1">Bezetting</h6>
                    <h3 class="mb-0 text-primary">{{ $pegawais->count() }}</h3>
                    <small class="text-muted">Pegawai Aktif</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 text-center">
                    <h6 class="text-muted mb-1">Kebutuhan</h6>
                    <h3 class="mb-0 text-info">{{ $kebutuhan ? $kebutuhan->jumlah_kebutuhan : 0 }}</h3>
                    <small class="text-muted">Pegawai Dibutuhkan</small>
                </div>
            </div>
            <div class="col-md-4">
                @php
                    $selisih = $pegawais->count() - ($kebutuhan ? $kebutuhan->jumlah_kebutuhan : 0);
                    $statusClass = $selisih == 0 ? 'success' : ($selisih > 0 ? 'warning' : 'danger');
                @endphp
                <div class="border rounded p-3 text-center">
                    <h6 class="text-muted mb-1">Selisih</h6>
                    <h3 class="mb-0 text-{{ $statusClass }}">{{ $selisih >= 0 ? '+' : '' }}{{ $selisih }}</h3>
                    <small class="text-muted">{{ $selisih == 0 ? 'Terpenuhi' : ($selisih > 0 ? 'Kelebihan' : 'Kekurangan') }}</small>
                </div>
            </div>
        </div>

        <!-- Daftar Pegawai -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="50" class="text-center">No</th>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Golongan</th>
                        <th>Pendidikan</th>
                        <th class="text-center">TMT Jabatan</th>
                        <th class="text-center" width="80">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pegawais as $i => $pegawai)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td><strong>{{ $pegawai->nip }}</strong></td>
                        <td>{{ $pegawai->nama }}</td>
                        <td>
                            @if($pegawai->golongan)
                                <span class="badge bg-secondary">{{ $pegawai->golongan->nama }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $pegawai->pendidikan_terakhir ?? '-' }}</td>
                        <td class="text-center">
                            {{ $pegawai->tmt_jabatan ? $pegawai->tmt_jabatan->format('d/m/Y') : '-' }}
                        </td>
                        <td class="text-center">
                            <a href="{{ route('pegawai.show', $pegawai) }}" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                            Tidak ada pegawai untuk jabatan ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
