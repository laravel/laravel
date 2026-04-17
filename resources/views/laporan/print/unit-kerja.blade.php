@extends('laporan.print.layout')

@section('title', 'Laporan Berdasarkan Unit Kerja')

@section('content')
<div class="report-title">
    <h3>Laporan Pegawai Berdasarkan Unit Kerja</h3>
    <p>Tanggal: {{ now()->format('d F Y') }}</p>
</div>

<div class="summary">
    <div class="summary-item">
        <span class="summary-label">Total Unit Kerja:</span>
        <span class="summary-value">{{ number_format(count($data)) }}</span>
    </div>
    <div class="summary-item">
        <span class="summary-label">Total Pegawai:</span>
        <span class="summary-value">{{ number_format($data->sum('total')) }}</span>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th style="width: 40px;">No</th>
            <th>Unit Kerja</th>
            <th style="width: 120px;">Jumlah Pegawai</th>
            <th style="width: 100px;">Persentase</th>
        </tr>
    </thead>
    <tbody>
        @php $totalPegawai = $data->sum('total'); @endphp
        @forelse($data as $index => $item)
        <tr>
            <td class="center">{{ $index + 1 }}</td>
            <td>{{ $item->unit_kerja_nama ?? 'Tidak Ada Unit Kerja' }}</td>
            <td class="center">{{ number_format($item->total) }}</td>
            <td class="center">{{ $totalPegawai > 0 ? number_format(($item->total / $totalPegawai) * 100, 1) : 0 }}%</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="center">Tidak ada data</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2">TOTAL</th>
            <th class="center">{{ number_format($totalPegawai) }}</th>
            <th class="center">100%</th>
        </tr>
    </tfoot>
</table>
@endsection
