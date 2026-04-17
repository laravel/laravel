@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
@if(auth()->user()->isAdmin())
{{-- Admin Dashboard --}}
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card bg-primary position-relative">
            <i class="bi bi-people stat-icon"></i>
            <h3 class="mb-1">{{ $total_pegawai }}</h3>
            <p class="mb-0">Total Pegawai</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card bg-success position-relative">
            <i class="bi bi-airplane stat-icon"></i>
            <h3 class="mb-1">{{ $total_perjalanan_dinas }}</h3>
            <p class="mb-0">Perjalanan Dinas</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card bg-warning position-relative">
            <i class="bi bi-calendar-x stat-icon"></i>
            <h3 class="mb-1">{{ $total_cuti }}</h3>
            <p class="mb-0">Pengajuan Cuti</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card bg-info position-relative">
            <i class="bi bi-cash-stack stat-icon"></i>
            <h3 class="mb-1">{{ $total_kgb }}</h3>
            <p class="mb-0">KGB Diproses</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-airplane me-2"></i>Perjalanan Dinas Pending</span>
                <a href="{{ route('perjalanan-dinas.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                @if($perjalanan_pending->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Pegawai</th>
                                <th>Tujuan</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($perjalanan_pending as $pd)
                            <tr>
                                <td>{{ $pd->pegawai->nama }}</td>
                                <td>{{ Str::limit($pd->tujuan, 20) }}</td>
                                <td>{{ $pd->tanggal_berangkat->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('perjalanan-dinas.show', $pd) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center mb-0">Tidak ada pengajuan pending</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar-x me-2"></i>Cuti Pending</span>
                <a href="{{ route('cuti.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                @if($cuti_pending->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Pegawai</th>
                                <th>Jenis Cuti</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cuti_pending as $c)
                            <tr>
                                <td>{{ $c->pegawai->nama }}</td>
                                <td>{{ $c->jenisCuti->nama }}</td>
                                <td>{{ $c->tanggal_mulai->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('cuti.show', $c) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center mb-0">Tidak ada pengajuan pending</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-cash-stack me-2"></i>KGB Diproses</span>
                <a href="{{ route('kgb.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                @if($kgb_pending->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>No. SK</th>
                                <th>Pegawai</th>
                                <th>TMT KGB</th>
                                <th>Gaji Baru</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kgb_pending as $k)
                            <tr>
                                <td>{{ $k->nomor_sk }}</td>
                                <td>{{ $k->pegawai->nama }}</td>
                                <td>{{ $k->tmt_kgb->format('d/m/Y') }}</td>
                                <td>Rp {{ number_format($k->gaji_pokok_baru, 0, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('kgb.show', $k) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center mb-0">Tidak ada KGB diproses</p>
                @endif
            </div>
        </div>
    </div>
</div>

@elseif(auth()->user()->isSubAdmin())
{{-- Sub Admin Dashboard --}}
<div class="alert alert-info mb-4">
    <i class="bi bi-info-circle me-2"></i>
    Dashboard Unit Kerja: <strong>{{ $unit_kerja->nama ?? 'Tidak Terkait' }}</strong>
</div>

@if(!empty($update_schedule) && $update_schedule->is_enabled)
    <div class="alert {{ !empty($update_schedule_read_only) ? 'alert-warning' : 'alert-success' }} mb-4 update-schedule-countdown-wrapper" data-countdown-end="{{ $update_schedule_countdown_iso }}">
        <i class="bi {{ !empty($update_schedule_read_only) ? 'bi-lock' : 'bi-clock-history' }} me-2"></i>
        @if(!empty($update_schedule_read_only))
            Periode update data telah berakhir pada <strong>{{ $update_schedule->formattedEndsAt() }}</strong>. Mode read-only sedang aktif.
        @else
            Periode update data masih dibuka hingga <strong>{{ $update_schedule->formattedEndsAt() }}</strong>.
        @endif
        <div class="mt-1">Sisa waktu: <strong class="update-schedule-countdown">menghitung...</strong></div>
    </div>
@endif

<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card bg-primary position-relative">
            <i class="bi bi-people stat-icon"></i>
            <h3 class="mb-1">{{ $total_pegawai }}</h3>
            <p class="mb-0">Total Pegawai</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card bg-success position-relative">
            <i class="bi bi-airplane stat-icon"></i>
            <h3 class="mb-1">{{ $total_perjalanan_dinas }}</h3>
            <p class="mb-0">Perjalanan Dinas</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card bg-warning position-relative">
            <i class="bi bi-calendar-x stat-icon"></i>
            <h3 class="mb-1">{{ $total_cuti }}</h3>
            <p class="mb-0">Pengajuan Cuti</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card bg-info position-relative">
            <i class="bi bi-cash-stack stat-icon"></i>
            <h3 class="mb-1">{{ $total_kgb }}</h3>
            <p class="mb-0">KGB Diproses</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-airplane me-2"></i>Perjalanan Dinas Pending</span>
                <a href="{{ route('perjalanan-dinas.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                @if($perjalanan_pending->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Pegawai</th>
                                <th>Tujuan</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($perjalanan_pending as $pd)
                            <tr>
                                <td>{{ $pd->pegawai->nama }}</td>
                                <td>{{ Str::limit($pd->tujuan, 20) }}</td>
                                <td>{{ $pd->tanggal_berangkat->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('perjalanan-dinas.show', $pd) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center mb-0">Tidak ada pengajuan pending</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar-x me-2"></i>Cuti Pending</span>
                <a href="{{ route('cuti.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                @if($cuti_pending->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Pegawai</th>
                                <th>Jenis Cuti</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cuti_pending as $c)
                            <tr>
                                <td>{{ $c->pegawai->nama }}</td>
                                <td>{{ $c->jenisCuti->nama }}</td>
                                <td>{{ $c->tanggal_mulai->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('cuti.show', $c) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center mb-0">Tidak ada pengajuan pending</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-cash-stack me-2"></i>KGB Diproses</span>
                <a href="{{ route('kgb.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                @if($kgb_pending->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>No. SK</th>
                                <th>Pegawai</th>
                                <th>TMT KGB</th>
                                <th>Gaji Baru</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kgb_pending as $k)
                            <tr>
                                <td>{{ $k->nomor_sk }}</td>
                                <td>{{ $k->pegawai->nama }}</td>
                                <td>{{ $k->tmt_kgb->format('d/m/Y') }}</td>
                                <td>Rp {{ number_format($k->gaji_pokok_baru, 0, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('kgb.show', $k) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center mb-0">Tidak ada KGB diproses</p>
                @endif
            </div>
        </div>
    </div>
</div>

@else
{{-- Pegawai Dashboard --}}
@if(!empty($update_schedule) && $update_schedule->is_enabled)
    <div class="alert {{ !empty($update_schedule_read_only) ? 'alert-warning' : 'alert-success' }} mb-4 update-schedule-countdown-wrapper" data-countdown-end="{{ $update_schedule_countdown_iso }}">
        <i class="bi {{ !empty($update_schedule_read_only) ? 'bi-lock' : 'bi-clock-history' }} me-2"></i>
        @if(!empty($update_schedule_read_only))
            Periode update data telah berakhir pada <strong>{{ $update_schedule->formattedEndsAt() }}</strong>. Mode read-only sedang aktif.
        @else
            Periode update data masih dibuka hingga <strong>{{ $update_schedule->formattedEndsAt() }}</strong>.
        @endif
        <div class="mt-1">Sisa waktu: <strong class="update-schedule-countdown">menghitung...</strong></div>
    </div>
@endif

@if($pegawai)
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body text-center">
                @if($pegawai->foto)
                <img src="{{ Storage::url($pegawai->foto) }}" alt="Foto" class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                @else
                <i class="bi bi-person-circle text-muted" style="font-size: 5rem;"></i>
                @endif
                <h5 class="mb-1">{{ $pegawai->nama }}</h5>
                <p class="text-muted mb-1">{{ $pegawai->nip }}</p>
                <span class="badge bg-primary">{{ $pegawai->jabatan->nama ?? '-' }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-8 mb-3">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-badge me-2"></i>Informasi Kepegawaian
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" width="40%">Golongan</td>
                                <td>{{ $pegawai->golongan->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jabatan</td>
                                <td>{{ $pegawai->jabatan->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Unit Kerja</td>
                                <td>{{ $pegawai->unitKerja->nama ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" width="40%">Status</td>
                                <td><span class="badge bg-success">{{ $pegawai->status_pegawai }}</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">TMT PNS</td>
                                <td>{{ $pegawai->tmt_pns ? $pegawai->tmt_pns->format('d/m/Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Masa Kerja</td>
                                <td>{{ $pegawai->masa_kerja ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="text-end">
                    <a href="{{ route('profil.edit') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i>Update Data
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-airplane me-2"></i>Perjalanan Dinas</span>
                <a href="{{ route('perjalanan-dinas.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus"></i>
                </a>
            </div>
            <div class="card-body">
                @if($perjalanan_dinas->count() > 0)
                @foreach($perjalanan_dinas as $pd)
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <div>
                        <strong>{{ Str::limit($pd->tujuan, 20) }}</strong><br>
                        <small class="text-muted">{{ $pd->tanggal_berangkat->format('d/m/Y') }}</small>
                    </div>
                    <span class="badge bg-{{ $pd->status == 'Disetujui' ? 'success' : ($pd->status == 'Ditolak' ? 'danger' : 'warning') }}">
                        {{ $pd->status }}
                    </span>
                </div>
                @endforeach
                <a href="{{ route('perjalanan-dinas.index') }}" class="btn btn-outline-primary btn-sm w-100 mt-2">Lihat Semua</a>
                @else
                <p class="text-muted text-center mb-0">Belum ada perjalanan dinas</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar-x me-2"></i>Cuti</span>
                <a href="{{ route('cuti.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus"></i>
                </a>
            </div>
            <div class="card-body">
                @if($cuti->count() > 0)
                @foreach($cuti as $c)
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <div>
                        <strong>{{ $c->jenisCuti->nama }}</strong><br>
                        <small class="text-muted">{{ $c->tanggal_mulai->format('d/m/Y') }} - {{ $c->tanggal_selesai->format('d/m/Y') }}</small>
                    </div>
                    <span class="badge bg-{{ $c->status == 'Disetujui' ? 'success' : ($c->status == 'Ditolak' ? 'danger' : 'warning') }}">
                        {{ $c->status }}
                    </span>
                </div>
                @endforeach
                <a href="{{ route('cuti.index') }}" class="btn btn-outline-primary btn-sm w-100 mt-2">Lihat Semua</a>
                @else
                <p class="text-muted text-center mb-0">Belum ada pengajuan cuti</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-cash-stack me-2"></i>Riwayat KGB
            </div>
            <div class="card-body">
                @if($kgb->count() > 0)
                @foreach($kgb as $k)
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <div>
                        <strong>{{ $k->nomor_sk }}</strong><br>
                        <small class="text-muted">TMT: {{ $k->tmt_kgb->format('d/m/Y') }}</small>
                    </div>
                    <span class="badge bg-{{ $k->status == 'Disetujui' ? 'success' : ($k->status == 'Ditolak' ? 'danger' : 'warning') }}">
                        {{ $k->status }}
                    </span>
                </div>
                @endforeach
                <a href="{{ route('kgb.index') }}" class="btn btn-outline-primary btn-sm w-100 mt-2">Lihat Semua</a>
                @else
                <p class="text-muted text-center mb-0">Belum ada riwayat KGB</p>
                @endif
            </div>
        </div>
    </div>
</div>
@else
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle me-2"></i>
    Data pegawai Anda belum terdaftar. Silakan hubungi administrator.
</div>
@endif
@endif
@endsection

@push('scripts')
<script>
(function () {
    const wrappers = document.querySelectorAll('.update-schedule-countdown-wrapper');
    if (!wrappers.length) return;

    const renderWrapper = (wrapper) => {
        const targetEl = wrapper.querySelector('.update-schedule-countdown');
        if (!targetEl) return;

        const endAt = wrapper.getAttribute('data-countdown-end');
        if (!endAt) {
            targetEl.textContent = '-';
            return;
        }

        const endTime = new Date(endAt).getTime();
        const now = Date.now();
        const diff = endTime - now;

        if (diff <= 0) {
            targetEl.textContent = 'Waktu update berakhir';
            return;
        }

        const totalSeconds = Math.floor(diff / 1000);
        const days = Math.floor(totalSeconds / 86400);
        const hours = Math.floor((totalSeconds % 86400) / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;

        const parts = [];
        if (days > 0) parts.push(days + ' hari');
        parts.push(hours + ' jam');
        parts.push(minutes + ' menit');
        parts.push(seconds + ' detik');

        targetEl.textContent = parts.join(' ');
    };

    const renderAll = () => wrappers.forEach(renderWrapper);
    renderAll();
    setInterval(renderAll, 1000);
})();
</script>
@endpush
