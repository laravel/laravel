@extends('laporan.print.layout')

@section('title', 'Laporan Prediksi Pensiun')

@section('content')
<div class="report-title">
    <h3>Laporan Prediksi Pensiun Pegawai</h3>
    <p>Tahun: {{ $year ?? now()->year }} | Dicetak: {{ now()->format('d F Y') }}</p>
</div>

<div class="summary">
    <div class="summary-item">
        <span class="summary-label">Total yang akan Pensiun:</span>
        <span class="summary-value">{{ number_format($data->count()) }}</span>
    </div>
    <div class="summary-item">
        <span class="summary-label">Batas Usia Pensiun:</span>
        <span class="summary-value">58 Tahun</span>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th style="width: 40px;">No</th>
            <th>NIP</th>
            <th>Nama</th>
            <th>Tanggal Lahir</th>
            <th style="width: 60px;">Usia</th>
            <th>Jabatan</th>
            <th>Prediksi Pensiun</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $index => $pegawai)
        @php
            $tglLahir = \Carbon\Carbon::parse($pegawai->tanggal_lahir);
            $pensiun = $tglLahir->copy()->addYears(58);
            $usia = $tglLahir->age;
        @endphp
        <tr>
            <td class="center">{{ $index + 1 }}</td>
            <td>{{ $pegawai->nip }}</td>
            <td>{{ $pegawai->nama }}</td>
            <td class="center">{{ $tglLahir->format('d/m/Y') }}</td>
            <td class="center">{{ $usia }}</td>
            <td>{{ $pegawai->jabatan->nama ?? '-' }}</td>
            <td class="center">{{ $pensiun->format('F Y') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="center">Tidak ada data pegawai yang akan pensiun</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
