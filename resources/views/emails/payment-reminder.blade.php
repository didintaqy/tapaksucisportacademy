@extends('layout.emails.master')

@section('content')
    <div class="email-container">
        <h1>Pengingat Pembayaran Tagihan</h1>

        <p>Halo Bapak/Ibu wali dari <strong>{{ $payment->student?->full_name }}</strong>,</p>

        <p>
            Kami mengingatkan bahwa terdapat tagihan di Tapak Suci Sport Academy yang belum diselesaikan:
        </p>

        <div class="email-field">
            <strong>No. Invoice</strong>
            <p>{{ $payment->invoice_number }}</p>
        </div>

        <div class="email-field">
            <strong>Jenis Pembayaran</strong>
            <p>{{ $payment->paymentType?->name ?? 'Pembayaran' }}</p>
        </div>

        @if ($payment->period)
            <div class="email-field">
                <strong>Periode</strong>
                <p>{{ $payment->period }}</p>
            </div>
        @endif

        <div class="email-field">
            <strong>Nominal</strong>
            <p>{{ $payment->formattedAmount() }}</p>
        </div>

        <div class="email-field">
            <strong>Jatuh Tempo</strong>
            <p>{{ $payment->due_date?->translatedFormat('d F Y') ?? '-' }}</p>
        </div>

        <div class="email-field">
            <strong>Status</strong>
            <p>
                @if ($daysUntilDue > 0)
                    Jatuh tempo dalam {{ $daysUntilDue }} hari.
                @elseif ($daysUntilDue === 0)
                    Jatuh tempo hari ini.
                @else
                    Sudah melewati jatuh tempo {{ abs($daysUntilDue) }} hari.
                @endif
            </p>
        </div>

        <p style="margin-top: 2rem;">
            Silakan melakukan pembayaran melalui portal pembayaran atau menghubungi admin akademi.
            Jika Anda sudah membayar, mohon abaikan email ini.
        </p>

        <p style="margin-top: 2rem;">
            Terima kasih. <br><br>
            — Tapak Suci Sport Academy
        </p>
    </div>
@endsection
