@extends('laporan.print.layout')

@section('title', 'Laporan Statistik Kepegawaian')

@section('content')
<div class="report-title">
    <h3>Laporan Statistik Kepegawaian</h3>
    <p>Tanggal: {{ now()->format('d F Y') }}</p>
</div>

<div class="row">
    <div class="col-6">
        <div class="stat-box">
            <h4>{{ number_format($stats['total_pegawai']) }}</h4>
            <p>Total Pegawai</p>
        </div>
    </div>
    <div class="col-6">
        <div class="stat-box">
            <h4>{{ number_format($stats['pns']) }}</h4>
            <p>PNS</p>
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-6">
        <div class="stat-box">
            <h4>{{ number_format($stats['laki_laki']) }}</h4>
            <p>Laki-laki</p>
        </div>
    </div>
    <div class="col-6">
        <div class="stat-box">
            <h4>{{ number_format($stats['perempuan']) }}</h4>
            <p>Perempuan</p>
        </div>
    </div>
</div>

<br><br>
<h4 style="margin-bottom: 15px;">Distribusi Status Pegawai</h4>
<table>
    <thead>
        <tr>
            <th>Status</th>
            <th style="width: 120px;">Jumlah</th>
            <th style="width: 100px;">Persentase</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>PNS</td>
            <td class="center">{{ number_format($stats['pns']) }}</td>
            <td class="center">{{ $stats['total_pegawai'] > 0 ? number_format(($stats['pns'] / $stats['total_pegawai']) * 100, 1) : 0 }}%</td>
        </tr>
        <tr>
            <td>CPNS</td>
            <td class="center">{{ number_format($stats['cpns']) }}</td>
            <td class="center">{{ $stats['total_pegawai'] > 0 ? number_format(($stats['cpns'] / $stats['total_pegawai']) * 100, 1) : 0 }}%</td>
        </tr>
        <tr>
            <td>PPPK</td>
            <td class="center">{{ number_format($stats['pppk']) }}</td>
            <td class="center">{{ $stats['total_pegawai'] > 0 ? number_format(($stats['pppk'] / $stats['total_pegawai']) * 100, 1) : 0 }}%</td>
        </tr>
        <tr>
            <td>PPPK Paruh Waktu</td>
            <td class="center">{{ number_format($stats['pppk_paruh_waktu']) }}</td>
            <td class="center">{{ $stats['total_pegawai'] > 0 ? number_format(($stats['pppk_paruh_waktu'] / $stats['total_pegawai']) * 100, 1) : 0 }}%</td>
        </tr>
        <tr>
            <td>Non ASN</td>
            <td class="center">{{ number_format($stats['non_asn']) }}</td>
            <td class="center">{{ $stats['total_pegawai'] > 0 ? number_format(($stats['non_asn'] / $stats['total_pegawai']) * 100, 1) : 0 }}%</td>
        </tr>
        <tr>
            <td>Berhenti/Keluar</td>
            <td class="center">{{ number_format($stats['berhenti_keluar']) }}</td>
            <td class="center">{{ $stats['total_pegawai'] > 0 ? number_format(($stats['berhenti_keluar'] / $stats['total_pegawai']) * 100, 1) : 0 }}%</td>
        </tr>
        <tr>
            <td>Pensiun</td>
            <td class="center">{{ number_format($stats['pensiun']) }}</td>
            <td class="center">{{ $stats['total_pegawai'] > 0 ? number_format(($stats['pensiun'] / $stats['total_pegawai']) * 100, 1) : 0 }}%</td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th>TOTAL</th>
            <th class="center">{{ number_format($stats['total_pegawai']) }}</th>
            <th class="center">100%</th>
        </tr>
    </tfoot>
</table>

<h4 style="margin-bottom: 15px;">Top 10 Golongan</h4>
<table>
    <thead>
        <tr>
            <th style="width: 40px;">No</th>
            <th>Golongan</th>
            <th style="width: 120px;">Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @foreach($topGolongan as $index => $item)
        <tr>
            <td class="center">{{ $index + 1 }}</td>
            <td>{{ $item['label'] }}</td>
            <td class="center">{{ number_format($item['value']) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<h4 style="margin-bottom: 15px;">Top 10 Jabatan</h4>
<table>
    <thead>
        <tr>
            <th style="width: 40px;">No</th>
            <th>Jabatan</th>
            <th style="width: 120px;">Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @foreach($topJabatan as $index => $item)
        <tr>
            <td class="center">{{ $index + 1 }}</td>
            <td>{{ $item['label'] }}</td>
            <td class="center">{{ number_format($item['value']) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
