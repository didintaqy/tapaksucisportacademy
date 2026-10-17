<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi {{ $payment->invoice_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            margin: 0;
            padding: 24px;
        }
        .toolbar { max-width: 720px; margin: 0 auto 16px; display: flex; justify-content: flex-end; gap: 8px; }
        .btn {
            border: none; border-radius: 8px; padding: 10px 18px; font-size: 14px;
            font-weight: 600; cursor: pointer; text-decoration: none;
        }
        .btn-primary { background: #fa5241; color: #fff; }
        .btn-gray { background: #e2e8f0; color: #334155; }
        .receipt {
            max-width: 720px; margin: 0 auto; background: #fff; padding: 40px;
            border-radius: 12px; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #00adef; padding-bottom: 16px; }
        .brand h1 { margin: 0; font-size: 20px; color: #0f172a; }
        .brand p { margin: 4px 0 0; font-size: 12px; color: #64748b; }
        .doc-title { text-align: right; }
        .doc-title h2 { margin: 0; font-size: 18px; letter-spacing: 2px; color: #fa5241; }
        .doc-title span { font-size: 12px; color: #64748b; }
        table.detail { width: 100%; border-collapse: collapse; margin-top: 24px; font-size: 14px; }
        table.detail td { padding: 8px 0; vertical-align: top; }
        table.detail td.label { width: 200px; color: #64748b; }
        table.detail td.sep { width: 14px; color: #64748b; }
        .amount-box {
            margin-top: 24px; padding: 18px 22px; border-radius: 10px;
            background: #ecfdf5; border: 1px solid #a7f3d0;
            display: flex; justify-content: space-between; align-items: center;
        }
        .amount-box .label { font-size: 13px; color: #047857; font-weight: 600; }
        .amount-box .value { font-size: 24px; font-weight: 800; color: #065f46; }
        .badge {
            display: inline-block; padding: 4px 14px; border-radius: 999px;
            font-size: 12px; font-weight: 700; letter-spacing: .5px;
        }
        .badge-paid { background: #dcfce7; color: #15803d; }
        .badge-unpaid { background: #fee2e2; color: #b91c1c; }
        .badge-pending { background: #fef3c7; color: #b45309; }
        .footer { margin-top: 40px; display: flex; justify-content: space-between; font-size: 12px; color: #64748b; }
        .sign { text-align: center; }
        .sign .line { margin-top: 56px; border-top: 1px solid #94a3b8; padding-top: 6px; min-width: 160px; }
        @media print {
            body { background: #fff; padding: 0; }
            .toolbar { display: none; }
            .receipt { box-shadow: none; border-radius: 0; max-width: 100%; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <a href="{{ url()->previous() }}" class="btn btn-gray">Kembali</a>
        <button onclick="window.print()" class="btn btn-primary">Cetak / Simpan PDF</button>
    </div>

    @php
        $status = $payment->status;
        $badgeClass = match (true) {
            $status->isPaid() => 'badge-paid',
            $status === \App\Enums\PaymentStatus::Pending => 'badge-pending',
            default => 'badge-unpaid',
        };
    @endphp

    <div class="receipt">
        <div class="header">
            <div class="brand">
                <h1>{{ config('payment.merchant.name', 'Tapak Suci Sport Academy') }}</h1>
                <p>Kwitansi Pembayaran Siswa</p>
            </div>
            <div class="doc-title">
                <h2>KWITANSI</h2>
                <span>{{ $payment->invoice_number }}</span>
            </div>
        </div>

        <table class="detail">
            <tr>
                <td class="label">Nama Siswa</td>
                <td class="sep">:</td>
                <td><strong>{{ $payment->student?->full_name ?? '-' }}</strong></td>
            </tr>
            <tr>
                <td class="label">ID Siswa</td>
                <td class="sep">:</td>
                <td>{{ $payment->student_id }}</td>
            </tr>
            <tr>
                <td class="label">Jenis Pembayaran</td>
                <td class="sep">:</td>
                <td>{{ $payment->paymentType?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Periode</td>
                <td class="sep">:</td>
                <td>{{ $payment->period ?: '-' }}</td>
            </tr>
            <tr>
                <td class="label">Keterangan</td>
                <td class="sep">:</td>
                <td>{{ $payment->description ?: '-' }}</td>
            </tr>
            <tr>
                <td class="label">Metode Bayar</td>
                <td class="sep">:</td>
                <td>{{ $payment->payment_method?->getLabel() ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Bayar</td>
                <td class="sep">:</td>
                <td>{{ $payment->paid_at ? $payment->paid_at->translatedFormat('d F Y H:i') : '-' }}</td>
            </tr>
            <tr>
                <td class="label">Status</td>
                <td class="sep">:</td>
                <td><span class="badge {{ $badgeClass }}">{{ $status->getLabel() }}</span></td>
            </tr>
        </table>

        <div class="amount-box">
            <span class="label">TOTAL PEMBAYARAN</span>
            <span class="value">{{ $payment->formattedAmount() }}</span>
        </div>

        <div class="footer">
            <div>
                Dicetak: {{ now()->translatedFormat('d F Y H:i') }} WIB
                @if ($payment->creator)
                    <br>Petugas: {{ $payment->creator->name }}
                @endif
            </div>
            <div class="sign">
                <div class="line">Admin Keuangan</div>
            </div>
        </div>
    </div>
</body>
</html>
