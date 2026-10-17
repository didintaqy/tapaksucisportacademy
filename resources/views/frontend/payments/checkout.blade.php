@extends('frontend.payments.layout')

@section('title', 'Pembayaran ' . $payment->invoice_number)

@php
    $qrString = data_get($payment->gateway_payload, 'qr_string');
    $qrUrl = data_get($payment->gateway_payload, 'qr_url');
    $instructions = [
        'Buka aplikasi mobile banking atau e-wallet (GoPay, OVO, DANA, ShopeePay, dll).',
        'Pilih menu Scan / Bayar QRIS.',
        'Arahkan kamera ke kode QR di halaman ini.',
        'Periksa nominal dan nama siswa, lalu selesaikan pembayaran.',
        'Setelah berhasil, status tagihan otomatis menjadi LUNAS. Muat ulang halaman untuk melihat status terbaru.',
    ];
@endphp

@section('content')
    <div class="card">
        <h2 class="section-title">Pembayaran QRIS</h2>
        <p class="subtitle">
            Invoice {{ $payment->invoice_number }} &middot;
            {{ $payment->paymentType?->name ?? 'Pembayaran' }}
            @if ($payment->period) &middot; {{ $payment->period }} @endif
        </p>

        @if ($payment->isPaid())
            <div class="alert alert-info" style="background:#dcfce7;color:#15803d;border-color:#bbf7d0;">
                Pembayaran ini sudah <strong>LUNAS</strong>. Terima kasih.
            </div>
            <div class="row" style="margin-top:10px;">
                <a href="{{ route('payments.portal.receipt', $payment) }}" target="_blank" class="btn btn-primary">Lihat Kwitansi</a>
                <a href="{{ route('payments.portal.bills') }}" class="btn btn-outline">Kembali ke Tagihan</a>
            </div>
        @else
            <div class="qr-wrap">
                <div class="qr-box">
                    @if ($qrUrl)
                        <img src="{{ $qrUrl }}" width="220" height="220" alt="QRIS {{ $payment->invoice_number }}">
                    @elseif ($qrString)
                        <div id="qrcode"></div>
                    @else
                        <p class="muted" style="padding:40px 20px;">Kode QR belum tersedia.<br>Silakan hubungi admin.</p>
                    @endif
                </div>
                <p class="muted" style="margin-top:12px;">Scan kode di atas dengan aplikasi pembayaran Anda</p>
            </div>

            <div style="text-align:center; margin-top:10px;">
                <span class="muted">Total Pembayaran</span>
                <div class="amount">{{ $payment->formattedAmount() }}</div>
                <span class="muted">a.n. {{ $student->full_name }}</span>
            </div>

            <ol class="steps">
                @foreach ($instructions as $step)
                    <li>{{ $step }}</li>
                @endforeach
            </ol>

            @if (! empty($payment->gateway_name))
                <p class="muted" style="margin-top:18px;">
                    Gateway: {{ ucfirst($payment->gateway_name) }}
                    @if ($payment->gateway_reference) &middot; Ref: {{ $payment->gateway_reference }} @endif
                </p>
            @endif

            <div class="row" style="margin-top:22px;">
                <a href="{{ route('payments.portal.bills') }}" class="btn btn-outline">Kembali ke Tagihan</a>
                <a href="{{ route('payments.portal.checkout.show', $payment) }}" class="btn btn-primary">Cek Status Pembayaran</a>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    @if (! $qrUrl && $qrString)
        <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs@master/qrcode.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var target = document.getElementById('qrcode');
                if (target && typeof QRCode !== 'undefined') {
                    new QRCode(target, {
                        text: @json($qrString),
                        width: 220,
                        height: 220,
                        correctLevel: QRCode.CorrectLevel.M
                    });
                }
            });
        </script>
    @endif
@endpush
