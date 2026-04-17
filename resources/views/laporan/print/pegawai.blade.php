@extends('laporan.print.layout')

@section('title', 'Laporan Data Pegawai')

@section('content')
<div class="report-title">
    <h3>Laporan Data Pegawai</h3>
    <p>Tanggal: {{ now()->format('d F Y') }}</p>
</div>

<div class="summary">
    <div class="summary-item">
        <span class="summary-label">Total Data:</span>
        <span class="summary-value">{{ number_format($data->count()) }}</span>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th style="width: 30px;">No</th>
            <th>NIP</th>
            <th>Nama</th>
            <th>Jabatan</th>
            <th>Golongan</th>
            <th>Unit Kerja</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $index => $pegawai)
        <tr>
            <td class="center">{{ $index + 1 }}</td>
            <td>{{ $pegawai->nip }}</td>
            <td>{{ $pegawai->nama }}</td>
            <td>{{ $pegawai->jabatan->nama ?? '-' }}</td>
            <td class="center">{{ $pegawai->golongan->nama ?? '-' }}</td>
            <td>{{ $pegawai->unitKerja->nama ?? '-' }}</td>
            <td class="center">{{ $pegawai->status_pegawai }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="center">Tidak ada data</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
