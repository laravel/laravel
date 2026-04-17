<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Jabatan - {{ $unitKerja->nama }}</title>
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
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 14px;
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
            justify-content: space-around;
            margin-bottom: 20px;
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
        }
        .summary-item {
            text-align: center;
        }
        .summary-item h3 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .summary-item p {
            font-size: 11px;
            color: #666;
        }
        .summary-item.primary h3 { color: #3498db; }
        .summary-item.info h3 { color: #17a2b8; }
        .summary-item.success h3 { color: #27ae60; }
        .summary-item.warning h3 { color: #f39c12; }
        .summary-item.danger h3 { color: #e74c3c; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #3498db;
            color: white;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
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
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
            color: #777;
        }
        tfoot th {
            background-color: #2c3e50;
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
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #3498db; color: white; border: none; cursor: pointer; border-radius: 5px;">
            <strong>🖨️ Cetak</strong>
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; cursor: pointer; border-radius: 5px; margin-left: 10px;">
            Tutup
        </button>
    </div>

    <div class="header">
        <h1>PETA JABATAN</h1>
        <h2>{{ $unitKerja->nama }}</h2>
        <p>Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
    </div>

    @php
        $totalSelisih = $totalBezetting - $totalKebutuhan;
        $statusClass = $totalSelisih == 0 ? 'success' : ($totalSelisih > 0 ? 'warning' : 'danger');
    @endphp

    <div class="summary">
        <div class="summary-item primary">
            <h3>{{ $totalBezetting }}</h3>
            <p>Total Bezetting</p>
        </div>
        <div class="summary-item info">
            <h3>{{ $totalKebutuhan }}</h3>
            <p>Total Kebutuhan</p>
        </div>
        <div class="summary-item {{ $statusClass }}">
            <h3>{{ $totalSelisih >= 0 ? '+' : '' }}{{ $totalSelisih }}</h3>
            <p>Selisih ({{ $totalSelisih == 0 ? 'Terpenuhi' : ($totalSelisih > 0 ? 'Kelebihan' : 'Kekurangan') }})</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="40">No</th>
                <th width="80">Kode</th>
                <th>Nama Jabatan</th>
                <th class="text-center" width="80">Bezetting</th>
                <th class="text-center" width="80">Kebutuhan</th>
                <th class="text-center" width="80">Selisih</th>
                <th class="text-center" width="100">Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $i => $item)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td><strong>{{ $item['kode'] }}</strong></td>
                <td>{{ $item['nama'] }}</td>
                <td class="text-center">{{ $item['bezetting'] }}</td>
                <td class="text-center">{{ $item['kebutuhan'] }}</td>
                <td class="text-center">{{ $item['selisih'] >= 0 ? '+' : '' }}{{ $item['selisih'] }}</td>
                <td class="text-center">
                    @php
                        $badgeClass = $item['selisih'] == 0 ? 'success' : ($item['selisih'] > 0 ? 'warning' : 'danger');
                    @endphp
                    <span class="badge badge-{{ $badgeClass }}">{{ $item['status'] }}</span>
                </td>
                <td>{{ $item['keterangan'] ?: '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
        @if(count($data) > 0)
        <tfoot>
            <tr>
                <th colspan="3" style="text-align: right;">Total:</th>
                <th class="text-center">{{ $totalBezetting }}</th>
                <th class="text-center">{{ $totalKebutuhan }}</th>
                <th class="text-center">{{ $totalSelisih >= 0 ? '+' : '' }}{{ $totalSelisih }}</th>
                <th colspan="2"></th>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        <p><strong>Keterangan:</strong></p>
        <p>Bezetting = Jumlah pegawai eksisting berdasarkan jabatan per unit kerja</p>
        <p>Kebutuhan = Jumlah pegawai yang dibutuhkan berdasarkan jabatan per unit kerja</p>
    </div>
</body>
</html>
