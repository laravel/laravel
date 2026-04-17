@extends('layouts.app')

@section('title', 'Detail Pegawai')
@section('page-title', 'Detail Pegawai')

@section('content')
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body text-center">
                @if($pegawai->foto)
                <img src="{{ Storage::url($pegawai->foto) }}" alt="Foto" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                @else
                <i class="bi bi-person-circle text-muted" style="font-size: 8rem;"></i>
                @endif
                <h4 class="mb-1">{{ $pegawai->nama }}</h4>
                <p class="text-muted mb-2">{{ $pegawai->nip }}</p>
                <span class="badge bg-{{ $pegawai->is_active ? 'success' : 'secondary' }} mb-3">
                    {{ $pegawai->status_pegawai }}
                </span>
                
                <hr>
                
                <div class="text-start">
                    <p class="mb-2"><i class="bi bi-layers me-2"></i><strong>Golongan:</strong> {{ $pegawai->golongan->nama ?? '-' }}</p>
                    <p class="mb-2"><i class="bi bi-briefcase me-2"></i><strong>Jabatan:</strong> {{ $pegawai->jabatan->nama ?? '-' }}</p>
                    <p class="mb-0"><i class="bi bi-diagram-3 me-2"></i><strong>Unit Kerja:</strong> {{ $pegawai->unitKerja->nama ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-person me-2"></i>Data Pribadi</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="40%" class="text-muted">NIK</td>
                                <td>{{ $pegawai->nik ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">NPWP</td>
                                <td>{{ $pegawai->npwp ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">No. Rekening</td>
                                <td>{{ $pegawai->no_rekening ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tempat, Tgl Lahir</td>
                                <td>{{ $pegawai->tempat_lahir ?? '-' }}, {{ $pegawai->tanggal_lahir ? $pegawai->tanggal_lahir->format('d/m/Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Umur</td>
                                <td>{{ $pegawai->umur ?? '-' }} tahun</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jenis Kelamin</td>
                                <td>{{ $pegawai->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Agama</td>
                                <td>{{ $pegawai->agama }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="40%" class="text-muted">Status Perkawinan</td>
                                <td>{{ $pegawai->status_perkawinan }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Pendidikan</td>
                                <td>{{ $pegawai->pendidikan_terakhir ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jurusan</td>
                                <td>{{ $pegawai->jurusan_pendidikan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Telepon</td>
                                <td>{{ $pegawai->telepon ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Email</td>
                                <td>{{ $pegawai->email ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="20%" class="text-muted">Alamat</td>
                                <td>{{ $pegawai->alamat ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Kabupaten/Kota</td>
                                <td>{{ $pegawai->kabupatenKota ? $pegawai->kabupatenKota->tipe . ' ' . $pegawai->kabupatenKota->nama : '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-briefcase me-2"></i>Data Kepegawaian</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="40%" class="text-muted">TMT CPNS</td>
                                <td>{{ $pegawai->tmt_cpns ? $pegawai->tmt_cpns->format('d/m/Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">TMT PNS</td>
                                <td>{{ $pegawai->tmt_pns ? $pegawai->tmt_pns->format('d/m/Y') : '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="40%" class="text-muted">TMT Golongan</td>
                                <td>{{ $pegawai->tmt_golongan ? $pegawai->tmt_golongan->format('d/m/Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">TMT Jabatan</td>
                                <td>{{ $pegawai->tmt_jabatan ? $pegawai->tmt_jabatan->format('d/m/Y') : '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="20%" class="text-muted">Masa Kerja</td>
                                <td>{{ $pegawai->masa_kerja ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Keterangan</td>
                                <td>{{ $pegawai->keterangan ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <a href="{{ route('pegawai.edit', $pegawai) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
            <a href="{{ route('pegawai.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>
</div>
@endsection
