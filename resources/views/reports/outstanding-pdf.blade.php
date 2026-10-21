<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Tunggakan</title>
    <style>
        @page { margin: 1.6cm; }
        body { font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif; font-size: 10px; color: #1f2937; }
        .header { border-bottom: 3px solid #9f1239; padding-bottom: 12px; margin-bottom: 18px; }
        .header h1 { margin: 0; font-size: 20px; color: #9f1239; }
        .header p { margin: 3px 0 0; font-size: 11px; color: #4b5563; }
        .summary { width: 100%; border-collapse: separate; border-spacing: 8px 0; margin-bottom: 18px; }
        .summary td { width: 25%; background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 6px; padding: 9px; text-align: center; }
        .summary .label { font-size: 8px; text-transform: uppercase; color: #6b7280; letter-spacing: .5px; }
        .summary .value { font-size: 14px; font-weight: bold; color: #111827; margin-top: 4px; }
        .summary .value.danger { color: #be123c; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border: 1px solid #e5e7eb; padding: 5px 7px; text-align: left; }
        table.data th { background: #9f1239; color: #fff; font-size: 9px; text-transform: uppercase; }
        table.data tr:nth-child(even) td { background: #f9fafb; }
        .right { text-align: right; }
        .center { text-align: center; }
        .total-row td { font-weight: bold; background: #ffe4e6 !important; }
        .footer { margin-top: 22px; padding-top: 10px; border-top: 1px solid #e5e7eb; font-size: 8px; color: #6b7280; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>TAPAK SUCI SPORT ACADEMY</h1>
        <p>Laporan Tunggakan per Siswa</p>
        <p>
            Per {{ $generatedAt->translatedFormat('d F Y') }}
            @if ($search)
                — Filter: "{{ $search }}"
            @endif
        </p>
    </div>

    <table class="summary">
        <tr>
            <td>
                <div class="label">Total Tunggakan</div>
                <div class="value danger">Rp {{ number_format($summary['total'], 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="label">Jumlah Tagihan</div>
                <div class="value">{{ number_format($summary['count'], 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="label">Siswa Menunggak</div>
                <div class="value">{{ number_format($summary['students'], 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="label">Lewat Jatuh Tempo</div>
                <div class="value">{{ number_format($summary['overdue'], 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th>Kode</th>
                <th>Nama Siswa</th>
                <th>Cabang</th>
                <th>Telepon</th>
                <th class="center" style="width: 8%;">Tagihan</th>
                <th>Jatuh Tempo Terlama</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $row->student?->student_code ?? '-' }}</td>
                    <td>{{ $row->student?->full_name ?? 'Siswa terhapus' }}</td>
                    <td>{{ $row->student?->branch ?? '-' }}</td>
                    <td>{{ $row->student?->phone_number ?? '-' }}</td>
                    <td class="center">{{ (int) $row->bills }}</td>
                    <td>{{ $row->oldest_due_date ? \Illuminate\Support\Carbon::parse($row->oldest_due_date)->format('d/m/Y') : '-' }}</td>
                    <td class="right">Rp {{ number_format((float) $row->total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="center">Tidak ada tunggakan.</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="5">Total</td>
                <td class="center">{{ number_format($summary['count'], 0, ',', '.') }}</td>
                <td></td>
                <td class="right">Rp {{ number_format($summary['total'], 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ $generatedAt->translatedFormat('d F Y H:i') }} WIB — Tapak Suci Sport Academy
    </div>
</body>
</html>
