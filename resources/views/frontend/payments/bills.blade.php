@extends('frontend.payments.layout')

@section('title', 'Tagihan ' . $student->full_name)

@php
    use App\Enums\PaymentStatus;

    $badgeClass = function (PaymentStatus $status): string {
        return match (true) {
            $status->isPaid() => 'badge-success',
            $status === PaymentStatus::Pending => 'badge-warning',
            $status === PaymentStatus::Unpaid => 'badge-danger',
            default => 'badge-gray',
        };
    };
@endphp

@section('content')
    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="card">
        <h2 class="section-title">{{ $student->full_name }}</h2>
        <p class="subtitle">
            ID Siswa: {{ $student->id }}
            @if ($student->nickname) &middot; {{ $student->nickname }} @endif
            @if ($student->belt_level) &middot; {{ $student->belt_level }} @endif
        </p>

        @if ($payments->isEmpty())
            <div class="alert alert-info">Belum ada tagihan untuk siswa ini.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>No. Invoice</th>
                        <th>Jenis</th>
                        <th>Periode</th>
                        <th>Nominal</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $payment)
                        <tr>
                            <td>{{ $payment->invoice_number }}</td>
                            <td>{{ $payment->paymentType?->name ?? '-' }}</td>
                            <td>{{ $payment->period ?: '-' }}</td>
                            <td><strong>{{ $payment->formattedAmount() }}</strong></td>
                            <td>
                                <span class="badge {{ $badgeClass($payment->status) }}">
                                    {{ $payment->status->getLabel() }}
                                </span>
                            </td>
                            <td>
                                @if ($payment->isPaid())
                                    <a class="btn btn-outline btn-sm" href="{{ route('payments.portal.receipt', $payment) }}" target="_blank">Kwitansi</a>
                                @elseif ($payment->status->isPayable())
                                    <form method="POST" action="{{ route('payments.portal.checkout', $payment) }}">
                                        @csrf
                                        <button class="btn btn-primary btn-sm" type="submit">Bayar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
