@extends('layout.emails.master')

@section('content')
    <div class="email-container">
        <h1>Pesan Kontak Baru</h1>

        <p>Admin, ada pesan baru dari pelanggan:</p>

        <div class="email-field">
            <strong>Nama:</strong> {{ $contactMessage->name }}
        </div>

        @if ($contactMessage->phone)
            <div class="email-field">
                <strong>Telepon:</strong> {{ $contactMessage->phone }}
            </div>
        @endif

        @if ($contactMessage->email)
            <div class="email-field">
                <strong>Email:</strong> {{ $contactMessage->email }}
            </div>
        @endif

        <div class="email-field">
            <strong>Status:</strong> {{ ucfirst($contactMessage->status) }}
        </div>

        @if ($contactMessage->message)
            <div class="email-field">
                <strong>Pesan:</strong>
                <p>{!! nl2br(e($contactMessage->message)) !!}</p>
            </div>
        @endif

        <p style="margin-top: 2rem;">
            Hubungi pelanggan melalui kontak yang tersedia atau balas langsung ke email ini (jika aktif).
        </p>

        <p style="margin-top: 2rem;">
            Terima kasih. <br><br>
            — Sistem Tapak Suci Sport Academy
        </p>
    </div>
@endsection