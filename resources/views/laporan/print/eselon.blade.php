@extends('laporan.print.layout')

@section('title', 'Laporan Berdasarkan Eselon')

@section('content')
<div class="report-title">
    <h3>Laporan Pegawai Berdasarkan Eselon dan Status</h3>
    <p>Tanggal: {{ now()->format('d F Y') }}</p>
    @if(isset($unitKerja) && $unitKerja)
    <p>Unit Kerja: {{ $unitKerja->nama }}</p>
    @endif
</div>

<div class="summary">
    <div class="summary-item">
        <span class="summary-label">Total Eselon:</span>
        <span class="summary-value">{{ number_format(count($eselonList)) }}</span>
    </div>
    <div class="summary-item">
        <span class="summary-label">Total Pegawai:</span>
        <span class="summary-value">{{ number_format($grandTotal) }}</span>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th rowspan="2" style="width: 40px;">No</th>
            <th rowspan="2">Eselon</th>
            <th colspan="{{ count($statusList) }}">Status Pegawai</th>
            <th rowspan="2" style="width: 80px;">Total</th>
        </tr>
        <tr>
            @foreach($statusList as $status)
            <th style="width: 70px; font-size: 10px;">{{ $status }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @foreach($eselonList as $eselon)
        <tr>
            <td class="center">{{ $no++ }}</td>
            <td><strong>{{ $eselon }}</strong></td>
            @foreach($statusList as $status)
            <td class="center">{{ number_format($data[$eselon][$status]) }}</td>
            @endforeach
            <td class="center"><strong>{{ number_format($totalPerEselon[$eselon]) }}</strong></td>
        </tr>
        @endforeach
        
        @if($totalNoEselon > 0)
        <tr style="background-color: #fff3cd;">
            <td class="center">{{ $no++ }}</td>
            <td><em>Tanpa Eselon</em></td>
            @foreach($statusList as $status)
            <td class="center">{{ number_format($dataNoEselon[$status]) }}</td>
            @endforeach
            <td class="center"><strong>{{ number_format($totalNoEselon) }}</strong></td>
        </tr>
        @endif
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2">TOTAL</th>
            @foreach($statusList as $status)
            <th class="center">{{ number_format($totalPerStatus[$status] ?? 0) }}</th>
            @endforeach
            <th class="center">{{ number_format($grandTotal) }}</th>
        </tr>
    </tfoot>
</table>
@endsection
