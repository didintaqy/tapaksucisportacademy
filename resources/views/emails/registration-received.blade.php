@extends('layout.emails.master')

@section('content')
    <div class="email-container">
        <h1>Pendaftaran Siswa Baru</h1>

        <p>Admin, ada calon siswa baru yang mendaftar melalui website:</p>

        <div class="email-field">
            <strong>Nama Lengkap:</strong> {{ $registration->full_name }}
        </div>

        @if ($registration->nickname)
            <div class="email-field">
                <strong>Nama Panggilan:</strong> {{ $registration->nickname }}
            </div>
        @endif

        <div class="email-field">
            <strong>Jenis Kelamin:</strong> {{ $registration->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
        </div>

        <div class="email-field">
            <strong>Tempat/Tanggal Lahir:</strong> {{ $registration->birth_place }}, {{ $registration->birth_date->format('d M Y') }}
        </div>

        @if ($registration->education)
            <div class="email-field">
                <strong>Pendidikan:</strong> {{ $registration->education }}
            </div>
        @endif

        @if ($registration->parent_name)
            <div class="email-field">
                <strong>Orang Tua / Wali:</strong> {{ $registration->parent_name }}
            </div>
        @endif

        <div class="email-field">
            <strong>Nomor Telepon:</strong> {{ $registration->phone }}
        </div>

        @if ($registration->email)
            <div class="email-field">
                <strong>Email:</strong> {{ $registration->email }}
            </div>
        @endif

        @if ($registration->branch)
            <div class="email-field">
                <strong>Cabang yang Diinginkan:</strong> {{ $registration->branch }}
            </div>
        @endif

        @if ($registration->address)
            <div class="email-field">
                <strong>Alamat:</strong> <p>{!! nl2br(e($registration->address)) !!}</p>
            </div>
        @endif

        @if ($registration->notes)
            <div class="email-field">
                <strong>Catatan:</strong> <p>{!! nl2br(e($registration->notes)) !!}</p>
            </div>
        @endif

        <div class="email-field">
            <strong>Status:</strong> {{ $registration->status->getLabel() }}
        </div>

        <p style="margin-top: 2rem;">
            Silakan hubungi pelanggan untuk verifikasi data, penjelasan biaya, dan jadwal latihan percobaan.
        </p>

        <p style="margin-top: 2rem;">
            Terima kasih. <br><br>
            — Sistem Tapak Suci Sport Academy
        </p>
    </div>
@endsection