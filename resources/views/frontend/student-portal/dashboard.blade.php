<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Siswa &middot; {{ $student->full_name }} &middot; Tapak Suci Sport Academy</title>
    <style>
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
        }
        .portal-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #fff;
            padding: 20px 0;
            border-bottom: 4px solid #fa5241;
        }
        .container {
            max-width: 1120px;
            margin: 0 auto;
            padding: 0 16px;
        }
        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .brand-badge {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: #fa5241;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 18px;
            color: #fff;
            flex-shrink: 0;
        }
        .brand h1 { font-size: 18px; margin: 0; }
        .brand p { margin: 2px 0 0; font-size: 12px; color: #cbd5e1; }
        .header-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 9px 15px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            border: 1px solid transparent;
            cursor: pointer;
        }
        .btn-primary { background: #fa5241; color: #fff; }
        .btn-outline-light { background: rgba(255,255,255,0.08); color: #fff; border-color: rgba(255,255,255,0.2); }
        .btn-outline { background: #fff; color: #334155; border-color: #cbd5e1; }
        .main-content { padding: 24px 0 48px; }
        .card {
            background: #fff;
            border-radius: 16px;
            padding: 22px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);
            margin-bottom: 20px;
        }
        .card-title {
            font-size: 16px;
            font-weight: 800;
            margin: 0 0 14px;
            color: #0f172a;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        .athlete-banner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }
        .athlete-meta h2 {
            margin: 4px 0;
            font-size: 22px;
            font-weight: 900;
            color: #0f172a;
        }
        .athlete-meta p {
            margin: 0;
            color: #64748b;
            font-size: 13px;
        }
        .stat-chips {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .stat-chip {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 10px 14px;
            min-width: 110px;
            text-align: center;
        }
        .stat-chip .label { font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; }
        .stat-chip .value { font-size: 15px; font-weight: 800; color: #0f172a; margin-top: 2px; }
        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }
        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }
        .grid-5 {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }
        .pillar-box {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 14px;
            text-align: center;
        }
        .pillar-box .name { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; }
        .pillar-box .score { font-size: 24px; font-weight: 900; color: #0f172a; margin: 6px 0; }
        .progress-track { width: 100%; height: 6px; background: #e2e8f0; border-radius: 999px; overflow: hidden; }
        .progress-bar { height: 100%; background: #fa5241; border-radius: 999px; }
        .table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { padding: 10px 12px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f8fafc; font-size: 11px; text-transform: uppercase; letter-spacing: 0.4px; color: #475569; font-weight: 700; }
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        .badge-gray { background: #e2e8f0; color: #475569; }
        .score-donut-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 10px;
        }
        @media (max-width: 900px) {
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
            .grid-5 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 480px) {
            .card { padding: 16px; }
            .athlete-meta h2 { font-size: 18px; }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header class="portal-header">
        <div class="container">
            <div class="header-bar">
                <div class="brand">
                    @if (filled($site['assets']['logo'] ?? null))
                        <img src="{{ $site['assets']['logo'] }}" alt="{{ $site['identity']['name'] ?? 'Tapak Suci Sport Academy' }}" style="width:46px;height:46px;border-radius:12px;object-fit:contain;background:#fff;padding:4px;">
                    @else
                        <div class="brand-badge">TS</div>
                    @endif
                    <div>
                        <h1>REMAJA &middot; Tapak Suci Sport Academy</h1>
                        <p>Rekam Medis, Analisis Jasmani &amp; Portal Akademik Siswa</p>
                    </div>
                </div>
                <div class="header-actions">
                    <a class="btn btn-primary" href="{{ route('student.portal.report-pdf') }}">Cetak Rapor PDF</a>
                    <a class="btn btn-outline-light" href="{{ route('payments.portal.bills') }}">Cek &amp; Bayar Iuran</a>
                    <a class="btn btn-outline-light" href="{{ route('student.portal.logout') }}">Keluar</a>
                </div>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <!-- Identitas Siswa -->
            <div class="card">
                <div class="athlete-banner">
                    <div class="athlete-meta">
                        <span class="badge badge-info">{{ $student->student_code ?? 'SIS-'.$student->id }}</span>
                        <h2>{{ $student->full_name }}</h2>
                        <p>
                            {{ $student->birth_place ?? 'Batam' }},
                            {{ $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->translatedFormat('d F Y') : '-' }}
                            &bull; {{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                            @if ($student->branch)
                                &bull; Cabang {{ $student->branch }}
                            @endif
                        </p>
                    </div>
                    <div class="stat-chips">
                        <div class="stat-chip">
                            <div class="label">Sabuk Saat Ini</div>
                            <div class="value">{{ $currentBelt }}</div>
                        </div>
                        <div class="stat-chip">
                            <div class="label">Program</div>
                            <div class="value">{{ $student->program?->name ?? 'Reguler' }}</div>
                        </div>
                        <div class="stat-chip">
                            <div class="label">Kehadiran</div>
                            <div class="value">{{ $attendanceStats['rate'] }}%</div>
                        </div>
                        <div class="stat-chip">
                            <div class="label">Total Medali</div>
                            <div class="value">{{ $medalCounts['total'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 1. ANALISIS KEBUGARAN REMAJA -->
            @if ($latestAnalysis)
                <div class="grid-2">
                    <!-- Ringkasan Skor & Antropometri -->
                    <div class="card">
                        <h3 class="card-title">
                            <span>Profil Kebugaran Jasmani (REMAJA)</span>
                            <span class="badge badge-gray">Sesi: {{ $latestAnalysis['test_date']->format('d/m/Y') }}</span>
                        </h3>
                        <div class="score-donut-wrap">
                            <div style="position: relative; width: 150px; height: 150px;">
                                <svg viewBox="0 0 36 36" style="width: 100%; height: 100%; transform: rotate(-90deg);">
                                    <path stroke="#e2e8f0" stroke-width="3.8" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                    <path stroke="{{ $latestAnalysis['score_category']['color'] }}" stroke-dasharray="{{ $latestAnalysis['overall_score'] }}, 100" stroke-width="3.8" stroke-linecap="round" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                </svg>
                                <div style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                    <span style="font-size: 30px; font-weight: 900; color: #0f172a;">{{ $latestAnalysis['overall_score'] }}%</span>
                                    <span style="font-size: 11px; font-weight: 800; color: {{ $latestAnalysis['score_category']['color'] }};">{{ $latestAnalysis['score_category']['label'] }}</span>
                                </div>
                            </div>
                            <p style="margin: 10px 0 0; font-size: 13px; color: #475569; font-weight: 600;">
                                {{ $latestAnalysis['score_category']['description'] }}
                            </p>
                        </div>

                        <div class="grid-3" style="margin-top: 14px;">
                            <div class="stat-chip">
                                <div class="label">TB / BB</div>
                                <div class="value">{{ $latestAnalysis['height'] ?? '-' }}m / {{ $latestAnalysis['weight'] ?? '-' }}kg</div>
                            </div>
                            <div class="stat-chip">
                                <div class="label">IMT / BMI</div>
                                <div class="value">{{ $latestAnalysis['bmi'] ?? '-' }} ({{ $latestAnalysis['bmi_category'] }})</div>
                            </div>
                            <div class="stat-chip">
                                <div class="label">Nadi Istirahat / Latihan</div>
                                <div class="value">{{ $latestAnalysis['heart_rate']['resting'] }} / {{ $latestAnalysis['heart_rate']['training'] }} bpm</div>
                            </div>
                        </div>
                    </div>

                    <!-- Grafik Radar Benchmark vs Capaian -->
                    <div class="card">
                        <h3 class="card-title">
                            <span>Diagram Radar Capaian vs Benchmark</span>
                            <span class="badge badge-info">Usia {{ $latestAnalysis['age'] ?? '-' }} thn</span>
                        </h3>
                        <div style="position: relative; height: 260px; width: 100%;">
                            <canvas id="studentRadarChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- 5 Pilar Kebugaran -->
                <div class="grid-5">
                    @foreach ($latestAnalysis['pillar_scores'] as $pillarName => $pillarScore)
                        <div class="pillar-box">
                            <div class="name">{{ $pillarName }}</div>
                            <div class="score">{{ $pillarScore }}%</div>
                            <div class="progress-track">
                                <div class="progress-bar" style="width: {{ min(100, $pillarScore) }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Rincian 10 Item Tes Fisik -->
                <div class="card">
                    <h3 class="card-title">Rincian Item Tes Fisik terhadap Benchmark</h3>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Komponen</th>
                                    <th>Item Tes</th>
                                    <th>Target Benchmark</th>
                                    <th>Hasil Atlet</th>
                                    <th>% Capaian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($latestAnalysis['items'] as $item)
                                    <tr>
                                        <td><strong>{{ $item['component_name'] }}</strong></td>
                                        <td>{{ $item['name'] }}</td>
                                        <td>{{ $item['target'] }} {{ $item['unit'] }}</td>
                                        <td><strong>{{ $item['has_value'] ? $item['result'].' '.$item['unit'] : '-' }}</strong></td>
                                        <td>
                                            <span class="badge {{ $item['pct'] >= 85 ? 'badge-success' : ($item['pct'] >= 65 ? 'badge-warning' : 'badge-danger') }}">
                                                {{ $item['pct'] }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. GRAFIK PERKEMBANGAN ANTAR SESI -->
                @if (count($progression['sessions']) > 1)
                    <div class="card">
                        <h3 class="card-title">
                            <span>Grafik Perkembangan Antar Sesi Tes</span>
                            <span class="badge badge-success">{{ count($progression['sessions']) }} Sesi Tes</span>
                        </h3>
                        <div style="position: relative; height: 240px; width: 100%;">
                            <canvas id="studentProgressionChart"></canvas>
                        </div>
                    </div>
                @endif
            @else
                <div class="card">
                    <h3 class="card-title">Profil Kebugaran Jasmani (REMAJA)</h3>
                    <p style="margin: 0; color: #64748b; font-size: 14px;">
                        Belum ada data pengukuran tes fisik untuk siswa ini. Hasil tes akan otomatis muncul setelah pelatih menginput data performa.
                    </p>
                </div>
            @endif

            <div class="grid-2">
                <!-- 3. PRESTASI & KEJUARAAN ATLET -->
                <div class="card">
                    <h3 class="card-title">
                        <span>Riwayat Prestasi &amp; Kejuaraan</span>
                        <span class="badge badge-warning">
                            {{ $medalCounts['emas'] }} Emas &bull; {{ $medalCounts['perak'] }} Perak &bull; {{ $medalCounts['perunggu'] }} Perunggu
                        </span>
                    </h3>
                    @if ($achievements->isNotEmpty())
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Kejuaraan</th>
                                        <th>Kategori</th>
                                        <th>Capaian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($achievements as $ach)
                                        <tr>
                                            <td>{{ $ach->event_date?->format('d/m/Y') }}</td>
                                            <td>
                                                <strong>{{ $ach->competition_name }}</strong>
                                                <div style="font-size: 11px; color: #64748b;">{{ $ach->level_label }}</div>
                                            </td>
                                            <td>{{ trim(($ach->category ?? '').($ach->weight_class ? " • {$ach->weight_class}" : '')) ?: '-' }}</td>
                                            <td><span class="badge badge-warning">{{ $ach->medal_label }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p style="margin: 0; color: #64748b; font-size: 13px;">Belum ada catatan kejuaraan untuk siswa ini.</p>
                    @endif
                </div>

                <!-- 4. RIWAYAT SABUK / UKT -->
                <div class="card">
                    <h3 class="card-title">Riwayat Ujian Kenaikan Tingkat (UKT)</h3>
                    @if ($beltPromotions->isNotEmpty())
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Tanggal UKT</th>
                                        <th>Kenaikan Sabuk</th>
                                        <th>Hasil</th>
                                        <th>Sertifikat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($beltPromotions as $promo)
                                        <tr>
                                            <td>{{ $promo->exam_date?->format('d/m/Y') }}</td>
                                            <td>{{ $promo->current_belt_label }} &rarr; <strong>{{ $promo->target_belt_label }}</strong></td>
                                            <td>
                                                <span class="badge {{ $promo->result?->value === 'lulus' ? 'badge-success' : ($promo->result?->value === 'ditunda' ? 'badge-warning' : 'badge-danger') }}">
                                                    {{ $promo->result_label }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($promo->result?->value === 'lulus' && $promo->certificate_number)
                                                    <a href="{{ route('student.portal.certificate', $promo) }}" target="_blank" class="btn btn-outline" style="padding: 4px 10px; font-size: 11px;">
                                                        Cetak Sertifikat
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p style="margin: 0; color: #64748b; font-size: 13px;">Belum ada riwayat ujian kenaikan tingkat (UKT).</p>
                    @endif
                </div>
            </div>

            <!-- 5. KEHADIRAN LATIHAN & TAGIHAN -->
            <div class="grid-2">
                <div class="card">
                    <h3 class="card-title">
                        <span>Riwayat Kehadiran Latihan</span>
                        <span class="badge badge-success">Hadir {{ $attendanceStats['hadir'] }} / {{ $attendanceStats['total'] }} Sesi</span>
                    </h3>
                    @if ($attendances->isNotEmpty())
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Kelas / Cabang</th>
                                        <th>Status</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($attendances as $att)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($att->date)->format('d/m/Y') }}</td>
                                            <td>{{ $att->trainingSchedule?->class_name ?? '-' }} ({{ $att->trainingSchedule?->branch ?? '-' }})</td>
                                            <td>
                                                <span class="badge {{ $att->status === 'hadir' ? 'badge-success' : ($att->status === 'alpa' ? 'badge-danger' : 'badge-info') }}">
                                                    {{ strtoupper($att->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $att->notes ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p style="margin: 0; color: #64748b; font-size: 13px;">Belum ada data presensi latihan.</p>
                    @endif
                </div>

                <div class="card">
                    <h3 class="card-title">
                        <span>Ringkasan Tagihan &amp; Iuran</span>
                        <a href="{{ route('payments.portal.bills') }}" class="btn btn-outline" style="padding: 5px 12px; font-size: 12px;">Buka Portal Pembayaran</a>
                    </h3>
                    @if ($payments->isNotEmpty())
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Invoice / Periode</th>
                                        <th>Jenis</th>
                                        <th>Nominal</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payments as $pay)
                                        <tr>
                                            <td>
                                                <strong>{{ $pay->invoice_number }}</strong>
                                                @if ($pay->period)
                                                    <div style="font-size: 11px; color: #64748b;">Periode {{ $pay->period }}</div>
                                                @endif
                                            </td>
                                            <td>{{ $pay->paymentType?->name ?? '-' }}</td>
                                            <td>{{ $pay->formattedAmount() }}</td>
                                            <td>
                                                <span class="badge {{ $pay->isPaid() ? 'badge-success' : 'badge-warning' }}">
                                                    {{ $pay->status?->getLabel() ?? $pay->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p style="margin: 0; color: #64748b; font-size: 13px;">Belum ada tagihan tercatat.</p>
                    @endif
                </div>
            </div>
        </div>
    </main>

    @if ($latestAnalysis)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const radarEl = document.getElementById('studentRadarChart');
                if (radarEl && window.Chart) {
                    new window.Chart(radarEl, {
                        type: 'radar',
                        data: {
                            labels: @json($latestAnalysis['chart_labels']),
                            datasets: [
                                {
                                    label: 'Benchmark (100%)',
                                    data: Array({{ count($latestAnalysis['chart_labels']) }}).fill(100),
                                    borderColor: 'rgba(59, 130, 246, 0.7)',
                                    backgroundColor: 'transparent',
                                    borderDash: [4, 4],
                                    pointRadius: 2
                                },
                                {
                                    label: 'Capaian Atlet (%)',
                                    data: @json($latestAnalysis['chart_scores']),
                                    borderColor: '#fa5241',
                                    backgroundColor: 'rgba(250, 82, 65, 0.2)',
                                    pointBackgroundColor: '#fa5241',
                                    pointRadius: 3
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                r: {
                                    min: 0,
                                    max: 100,
                                    ticks: { stepSize: 25, display: false }
                                }
                            }
                        }
                    });
                }

                const progEl = document.getElementById('studentProgressionChart');
                if (progEl && window.Chart) {
                    new window.Chart(progEl, {
                        type: 'line',
                        data: {
                            labels: @json($progression['labels']),
                            datasets: [
                                {
                                    label: 'Skor Kebugaran Total (%)',
                                    data: @json($progression['overall_series']),
                                    borderColor: '#fa5241',
                                    backgroundColor: 'rgba(250, 82, 65, 0.15)',
                                    fill: true,
                                    tension: 0.3
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: { min: 0, max: 100 }
                            }
                        }
                    });
                }
            });
        </script>
    @endif
</body>
</html>
