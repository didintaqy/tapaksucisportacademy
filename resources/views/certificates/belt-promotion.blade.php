<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat UKT {{ $promotion->certificate_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: "Georgia", "Times New Roman", serif;
            background: #eef2f7;
            color: #0f172a;
            margin: 0;
            padding: 24px;
        }
        .toolbar {
            max-width: 1000px;
            margin: 0 auto 16px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }
        .btn {
            border: none;
            border-radius: 8px;
            padding: 10px 18px;
            font-family: ui-sans-serif, system-ui, sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-primary { background: #fa5241; color: #fff; }
        .btn-gray { background: #e2e8f0; color: #334155; }
        .certificate {
            position: relative;
            max-width: 1000px;
            margin: 0 auto;
            background: #fff;
            padding: 26px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.12);
        }
        .frame {
            border: 2px solid #0f172a;
            padding: 10px;
        }
        .inner {
            position: relative;
            border: 6px double #fa5241;
            padding: 40px 56px 34px;
            text-align: center;
            overflow: hidden;
        }
        .inner::before,
        .inner::after {
            content: "";
            position: absolute;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            opacity: 0.08;
        }
        .inner::before { background: #00adef; top: -90px; left: -70px; }
        .inner::after { background: #fa5241; bottom: -100px; right: -60px; }
        .brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-bottom: 18px;
        }
        .brand img { width: 54px; height: 54px; object-fit: contain; }
        .brand-text { text-align: left; }
        .brand-text strong {
            display: block;
            font-family: ui-sans-serif, system-ui, sans-serif;
            font-size: 17px;
            letter-spacing: 1px;
        }
        .brand-text span {
            display: block;
            font-family: ui-sans-serif, system-ui, sans-serif;
            font-size: 11px;
            color: #64748b;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .doc-number {
            font-family: ui-sans-serif, system-ui, sans-serif;
            font-size: 11px;
            color: #64748b;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }
        h1 {
            font-size: 34px;
            letter-spacing: 4px;
            margin: 0 0 6px;
            color: #0f172a;
            text-transform: uppercase;
        }
        .subtitle {
            font-family: ui-sans-serif, system-ui, sans-serif;
            font-size: 12px;
            letter-spacing: 3px;
            color: #fa5241;
            text-transform: uppercase;
            margin-bottom: 28px;
        }
        .intro {
            font-size: 14px;
            color: #475569;
            margin: 0 0 10px;
        }
        .student-name {
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 4px;
            color: #0f172a;
        }
        .student-meta {
            font-family: ui-sans-serif, system-ui, sans-serif;
            font-size: 12px;
            color: #64748b;
            margin-bottom: 26px;
        }
        .description {
            max-width: 720px;
            margin: 0 auto 24px;
            font-size: 15px;
            line-height: 1.7;
            color: #334155;
        }
        .description strong { color: #0f172a; }
        .belt-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 18px;
            margin: 22px 0 26px;
            font-family: ui-sans-serif, system-ui, sans-serif;
        }
        .belt-box {
            min-width: 150px;
            padding: 10px 18px;
            border-radius: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .belt-box span {
            display: block;
            font-size: 10px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 4px;
        }
        .belt-box strong { font-size: 16px; color: #0f172a; }
        .arrow { font-size: 24px; color: #fa5241; }
        .result {
            display: inline-block;
            padding: 8px 26px;
            border-radius: 999px;
            background: #dcfce7;
            color: #15803d;
            font-family: ui-sans-serif, system-ui, sans-serif;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 30px;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 40px;
            margin-top: 12px;
            text-align: center;
            font-family: ui-sans-serif, system-ui, sans-serif;
            font-size: 12px;
            color: #475569;
        }
        .signature { min-width: 220px; }
        .signature .space { height: 58px; }
        .signature .line {
            border-top: 1px solid #94a3b8;
            padding-top: 6px;
            font-weight: 700;
            color: #0f172a;
        }
        .signature .role { color: #64748b; margin-top: 2px; }
        .issued {
            margin-top: 24px;
            font-family: ui-sans-serif, system-ui, sans-serif;
            font-size: 11px;
            color: #94a3b8;
        }
        @media print {
            @page { size: A4 landscape; margin: 10mm; }
            body { background: #fff; padding: 0; }
            .toolbar { display: none; }
            .certificate { box-shadow: none; max-width: 100%; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <a href="{{ url()->previous() }}" class="btn btn-gray">Kembali</a>
        <button onclick="window.print()" class="btn btn-primary">Cetak / Simpan PDF</button>
    </div>

    <div class="certificate">
        <div class="frame">
            <div class="inner">
                <div class="brand">
                    <img src="{{ asset('images/logo.svg') }}" alt="Logo Tapak Suci">
                    <div class="brand-text">
                        <strong>TAPAK SUCI SPORT ACADEMY</strong>
                        <span>Pencak Silat &amp; Pembinaan Atlet</span>
                    </div>
                </div>

                <div class="doc-number">Nomor: {{ $promotion->certificate_number }}</div>

                <h1>Sertifikat</h1>
                <div class="subtitle">Ujian Kenaikan Tingkat (UKT)</div>

                <p class="intro">Diberikan kepada:</p>
                <div class="student-name">{{ $promotion->student?->full_name ?? '—' }}</div>
                <div class="student-meta">
                    NIA: {{ $promotion->student?->student_code ?? '—' }}
                    @if ($promotion->student?->branch)
                        &nbsp;•&nbsp; Cabang: {{ $promotion->student->branch }}
                    @endif
                    @if ($promotion->student?->program?->name)
                        &nbsp;•&nbsp; Program: {{ $promotion->student->program->name }}
                    @endif
                </div>

                <p class="description">
                    atas keberhasilan mengikuti <strong>Ujian Kenaikan Tingkat</strong> yang dilaksanakan pada
                    <strong>{{ $promotion->exam_date?->translatedFormat('d F Y') ?? '—' }}</strong>
                    dan dinyatakan:
                </p>

                <div class="belt-row">
                    <div class="belt-box">
                        <span>Sabuk Sebelumnya</span>
                        <strong>{{ $promotion->current_belt_label }}</strong>
                    </div>
                    <div class="arrow">&rarr;</div>
                    <div class="belt-box">
                        <span>Sabuk Baru</span>
                        <strong>{{ $promotion->target_belt_label }}</strong>
                    </div>
                </div>

                <div class="result">Lulus</div>

                <div class="footer">
                    <div class="signature">
                        <div class="space"></div>
                        <div class="line">{{ $promotion->examiner?->name ?? 'Penguji' }}</div>
                        <div class="role">Penguji UKT</div>
                    </div>

                    <div class="signature">
                        <div class="space"></div>
                        <div class="line">Pengurus Tapak Suci</div>
                        <div class="role">Sport Academy</div>
                    </div>
                </div>

                <div class="issued">
                    Diterbitkan pada
                    {{ $promotion->certificate_issued_at?->translatedFormat('d F Y') ?? $promotion->exam_date?->translatedFormat('d F Y') }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
