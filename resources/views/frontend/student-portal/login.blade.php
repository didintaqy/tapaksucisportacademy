@extends('frontend.payments.layout')

@section('title', 'Portal Siswa & Analisis Kebugaran (REMAJA)')

@section('content')
    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="card">
        <span class="badge badge-warning" style="margin-bottom: 10px;">REMAJA &middot; Rekam Medis &amp; Analisis Jasmani Atlet</span>
        <h2 class="section-title">Portal Siswa Tapak Suci Sport Academy</h2>
        <p class="subtitle">
            Masuk menggunakan Kode Siswa atau NIK beserta tanggal lahir untuk melihat profil kebugaran fisik,
            grafik perkembangan, riwayat kehadiran, prestasi kejuaraan, sabuk/UKT, dan mengunduh rapor PDF.
        </p>

        <form method="POST" action="{{ route('student.portal.login') }}">
            @csrf
            <label for="student_id">Kode Siswa / ID / NIK</label>
            <input
                type="text"
                id="student_id"
                name="student_id"
                value="{{ old('student_id') }}"
                placeholder="contoh: SIS-00001 atau 2171100903170001"
                autofocus
                autocomplete="off"
                maxlength="32"
                required
            >
            @error('student_id')
                <div class="muted" style="color:#b91c1c; margin-top:6px;">{{ $message }}</div>
            @enderror

            <label for="birth_date" style="margin-top:14px;">Tanggal Lahir</label>
            <input
                type="date"
                id="birth_date"
                name="birth_date"
                value="{{ old('birth_date') }}"
                max="{{ now()->subDay()->toDateString() }}"
                autocomplete="off"
                required
                style="width: 100%; padding: 12px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 15px;"
            >
            @error('birth_date')
                <div class="muted" style="color:#b91c1c; margin-top:6px;">{{ $message }}</div>
            @enderror

            <p class="muted" style="margin-top:10px; font-size:0.9em;">
                Kombinasi Kode Siswa/NIK dan tanggal lahir menjaga privasi rekam medis serta rapor performa atlet.
            </p>

            <div class="row" style="margin-top:18px; align-items:center;">
                <button class="btn btn-primary" type="submit">Masuk Portal Siswa</button>
                <a class="btn btn-outline" href="{{ route('home') }}">Kembali ke Beranda</a>
            </div>
        </form>
    </div>
@endsection
