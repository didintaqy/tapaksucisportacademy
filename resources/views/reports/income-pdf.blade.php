<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Pemasukan</title>
    <style>
        @page { margin: 1.6cm; }
        body { font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif; font-size: 10px; color: #1f2937; }
        .header { border-bottom: 3px solid #1e3a8a; padding-bottom: 12px; margin-bottom: 18px; }
        .header h1 { margin: 0; font-size: 20px; color: #1e3a8a; }
        .header p { margin: 3px 0 0; font-size: 11px; color: #4b5563; }
        .summary { width: 100%; border-collapse: separate; border-spacing: 8px 0; margin-bottom: 18px; }
        .summary td { width: 33%; background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px; text-align: center; }
        .summary .label { font-size: 8px; text-transform: uppercase; color: #6b7280; letter-spacing: .5px; }
        .summary .value { font-size: 15px; font-weight: bold; color: #111827; margin-top: 4px; }
        .summary .value.success { color: #047857; }
        h2 { font-size: 12px; color: #1e3a8a; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; margin: 18px 0 8px; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border: 1px solid #e5e7eb; padding: 5px 7px; text-align: left; }
        table.data th { background: #1e3a8a; color: #fff; font-size: 9px; text-transform: uppercase; }
        table.data tr:nth-child(even) td { background: #f9fafb; }
        .right { text-align: right; }
        .center { text-align: center; }
        .total-row td { font-weight: bold; background: #eef2ff !important; }
        .footer { margin-top: 22px; padding-top: 10px; border-top: 1px solid #e5e7eb; font-size: 8px; color: #6b7280; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>TAPAK SUCI SPORT ACADEMY</h1>
        <p>Rekap Pemasukan</p>
        <p>Periode: {{ $from->translatedFormat('d F Y') }} — {{ $to->translatedFormat('d F Y') }}</p>
    </div>

    <table class="summary">
        <tr>
            <td>
                <div class="label">Total Pemasukan</div>
                <div class="value success">Rp {{ number_format($summary['total'], 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="label">Jumlah Transaksi</div>
                <div class="value">{{ number_format($summary['count'], 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="label">Rata-rata per Transaksi</div>
                <div class="value">Rp {{ number_format($summary['average'], 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <h2>Rekap per Bulan</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Periode</th>
                <th class="right">Transaksi</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($byMonth as $row)
                <tr>
                    <td>{{ $row['label'] }}</td>
                    <td class="right">{{ number_format($row['transactions'], 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($row['total'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td>Total</td>
                <td class="right">{{ number_format($summary['count'], 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($summary['total'], 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <h2>Rekap per Jenis Pembayaran</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Jenis Pembayaran</th>
                <th class="right">Transaksi</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($byType as $row)
                <tr>
                    <td>{{ $row->paymentType?->name ?? 'Tanpa jenis' }}</td>
                    <td class="right">{{ number_format((int) $row->transactions, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format((float) $row->total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="center">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ $generatedAt->translatedFormat('d F Y H:i') }} WIB — Tapak Suci Sport Academy
    </div>
</body>
</html>
