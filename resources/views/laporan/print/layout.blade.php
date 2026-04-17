<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laporan Kepegawaian')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 10mm;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 14px;
            font-weight: normal;
        }
        .header p {
            font-size: 11px;
            color: #666;
        }
        .report-title {
            text-align: center;
            margin-bottom: 20px;
        }
        .report-title h3 {
            font-size: 16px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .report-title p {
            font-size: 11px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
        }
        table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }
        table td.center {
            text-align: center;
        }
        table td.right {
            text-align: right;
        }
        .summary {
            background-color: #f9f9f9;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .summary-item {
            display: inline-block;
            margin-right: 30px;
            margin-bottom: 10px;
        }
        .summary-label {
            font-weight: bold;
            color: #666;
        }
        .summary-value {
            font-size: 18px;
            color: #333;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }
        .signature {
            float: right;
            text-align: center;
            width: 200px;
        }
        .signature-line {
            margin-top: 60px;
            border-bottom: 1px solid #333;
            margin-bottom: 5px;
        }
        .print-date {
            font-size: 10px;
            color: #666;
        }
        @media print {
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
            .page-break { page-break-before: always; }
        }
        .btn-print {
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 10px 20px;
            background: #4e73df;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-print:hover {
            background: #2e59d9;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: -10px;
        }
        .col-6 {
            flex: 0 0 50%;
            padding: 10px;
        }
        .stat-box {
            background: #f8f9fc;
            border: 1px solid #e3e6f0;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
        }
        .stat-box h4 {
            font-size: 24px;
            color: #4e73df;
            margin-bottom: 5px;
        }
        .stat-box p {
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <button class="btn-print no-print" onclick="window.print()">
        🖨️ Cetak
    </button>

    <div class="container">
        <div class="header">
            <h1>SISTEM INFORMASI KEPEGAWAIAN</h1>
            <h2>Pemerintah Daerah</h2>
            <p>Alamat: Jl. Contoh No. 123 | Telp: (021) 1234567 | Email: info@kepegawaian.go.id</p>
        </div>

        @yield('content')

        <div class="footer">
            <p class="print-date">Dicetak pada: {{ now()->format('d F Y H:i:s') }}</p>
            <div class="signature">
                <p>Mengetahui,</p>
                <p>Kepala Bagian Kepegawaian</p>
                <div class="signature-line"></div>
                <p>NIP. ........................</p>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>
</body>
</html>
