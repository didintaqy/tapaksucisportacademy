@extends('frontend.payments.layout')

@section('title', 'Portal Pembayaran')

@section('content')
    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="card">
        <h2 class="section-title">Cek &amp; Bayar Tagihan</h2>
        <p class="subtitle">
            Masukkan ID Siswa atau NIK untuk melihat tagihan dan melakukan pembayaran online.
        </p>

        <form method="POST" action="{{ route('payments.portal.search') }}">
            @csrf
            <label for="student_id">ID Siswa / NIK</label>
            <input
                type="text"
                id="student_id"
                name="student_id"
                value="{{ old('student_id') }}"
                placeholder="contoh: 1 atau 2171100903170001"
                autofocus
                autocomplete="off"
                maxlength="32"
                required
            >
            @error('student_id')
                <div class="muted" style="color:#b91c1c; margin-top:6px;">{{ $message }}</div>
            @enderror

            <div style="margin-top:18px;">
                <button class="btn btn-primary" type="submit">Cari Tagihan</button>
            </div>
        </form>
    </div>
@endsection
