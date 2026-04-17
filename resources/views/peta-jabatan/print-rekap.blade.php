<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $rekapTitle ?? 'Rekap Peta Jabatan' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            padding: 20px;
            color: #222;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 12px;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 4px;
        }
        .header h2 {
            font-size: 13px;
            font-weight: normal;
            color: #555;
        }
        .header p {
            font-size: 11px;
            color: #777;
            margin-top: 5px;
        }
        .summary {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
        }
        .summary-item {
            flex: 1;
            text-align: center;
            background: #f5f5f5;
            border: 1px solid #e2e2e2;
            border-radius: 6px;
            padding: 10px;
        }
        .summary-item h3 {
            font-size: 22px;
            margin-bottom: 4px;
        }
        .summary-item p {
            font-size: 11px;
            color: #666;
        }
        .primary h3 { color: #0d6efd; }
        .info h3 { color: #17a2b8; }
        .success h3 { color: #198754; }
        .warning h3 { color: #f39c12; }
        .danger h3 { color: #dc3545; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 7px;
            text-align: left;
        }
        th {
            background-color: #0d6efd;
            color: #fff;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tfoot th {
            background-color: #2c3e50;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .footer {
            margin-top: 20px;
            font-size: 11px;
            color: #666;
        }
        .no-print {
            margin-bottom: 18px;
        }
        .btn {
            padding: 9px 14px;
            border: none;
            border-radius: 5px;
            color: #fff;
            cursor: pointer;
            font-size: 12px;
        }
        .btn-print {
            background: #0d6efd;
        }
        .btn-close {
            background: #6c757d;
            margin-left: 8px;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn btn-print" onclick="window.print()">Cetak</button>
        <button class="btn btn-close" onclick="window.close()">Tutup</button>
    </div>

    <div class="header">
        <h1>REKAP PETA JABATAN</h1>
        <h2>{{ $rekapTitle ?? 'Semua Unit Kerja' }}</h2>
        <p>Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
    </div>

    @php
        $grandSelisih = $grandTotalBezetting - $grandTotalKebutuhan;
        $statusClass = $grandSelisih == 0 ? 'success' : ($grandSelisih > 0 ? 'warning' : 'danger');
    @endphp

    <div class="summary">
        <div class="summary-item primary">
            <h3>{{ $grandTotalBezetting }}</h3>
            <p>Total Bezetting</p>
        </div>
        <div class="summary-item info">
            <h3>{{ $grandTotalKebutuhan }}</h3>
            <p>Total Kebutuhan</p>
        </div>
        <div class="summary-item {{ $statusClass }}">
            <h3>{{ $grandSelisih >= 0 ? '+' : '' }}{{ $grandSelisih }}</h3>
            <p>Selisih</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="40">No</th>
                <th>Unit Kerja</th>
                <th class="text-center" width="110">Bezetting</th>
                <th class="text-center" width="110">Kebutuhan</th>
                <th class="text-center" width="110">Selisih</th>
                <th class="text-center" width="120">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapData as $i => $item)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $item['unit_kerja']->nama }}</td>
                <td class="text-center">{{ $item['bezetting'] }}</td>
                <td class="text-center">{{ $item['kebutuhan'] }}</td>
                <td class="text-center">{{ $item['selisih'] >= 0 ? '+' : '' }}{{ $item['selisih'] }}</td>
                <td class="text-center">
                    <span class="badge badge-{{ $item['status_class'] }}">{{ $item['status'] }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data unit kerja.</td>
            </tr>
            @endforelse
        </tbody>
        @if(count($rekapData) > 0)
        <tfoot>
            <tr>
                <th colspan="2" style="text-align: right;">Grand Total:</th>
                <th class="text-center">{{ $grandTotalBezetting }}</th>
                <th class="text-center">{{ $grandTotalKebutuhan }}</th>
                <th class="text-center">{{ $grandSelisih >= 0 ? '+' : '' }}{{ $grandSelisih }}</th>
                <th></th>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        <p>Bezetting = Jumlah pegawai eksisting berdasarkan jabatan per unit kerja</p>
        <p>Kebutuhan = Jumlah pegawai yang dibutuhkan berdasarkan jabatan per unit kerja</p>
    </div>
</body>
</html>
