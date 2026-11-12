<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapor Performa - {{ $student->full_name }}</title>
    <style>
        @page {
            margin: 2cm;
        }
        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #1e3a8a;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #1e3a8a;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
            color: #666;
        }
        .student-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8fafc;
            border-radius: 8px;
        }
        .info-group {
            flex: 1;
        }
        .info-group label {
            display: block;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 3px;
        }
        .info-group span {
            font-size: 13px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e3a8a;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #e2e8f0;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background: #1e3a8a;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) td {
            background: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-hadir { background: #dcfce7; color: #166534; }
        .badge-izin { background: #dbeafe; color: #1e40af; }
        .badge-sakit { background: #fef3c7; color: #92400e; }
        .badge-alpa { background: #fee2e2; color: #991b1b; }
        .badge-lulus { background: #dcfce7; color: #166534; }
        .badge-tidak_lulus { background: #fee2e2; color: #991b1b; }
        .badge-ditunda { background: #fef3c7; color: #92400e; }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }
        .stat-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #1e3a8a;
        }
        .stat-label {
            font-size: 9px;
            text-transform: uppercase;
            color: #666;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .sig-block {
            text-align: center;
            width: 200px;
        }
        .sig-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>TAPAK SUCI SPORT ACADEMY</h1>
        <p>Rapor Performa Siswa</p>
        <p>Periode: {{ \Carbon\Carbon::parse($date_from)->format('d F Y') }} s.d. {{ \Carbon\Carbon::parse($date_to)->format('d F Y') }}</p>
    </div>

    <div class="student-info">
        <div class="info-group">
            <label>Nama Lengkap</label>
            <span>{{ $student->full_name }}</span>
        </div>
        <div class="info-group">
            <label>Kode Siswa</label>
            <span>{{ $student->student_code }}</span>
        </div>
        <div class="info-group">
            <label>Sabuk Saat Ini</label>
            <span>{{ $current_belt }}</span>
        </div>
        <div class="info-group">
            <label>Cabang</label>
            <span>{{ $student->branch }}</span>
        </div>
        <div class="info-group">
            <label>Tanggal Lahir</label>
            <span>{{ \Carbon\Carbon::parse($student->birth_date)->format('d F Y') }}</span>
        </div>
        <div class="info-group">
            <label>Jenis Kelamin</label>
            <span>{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Statistik Kehadiran</div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">{{ $stats['total_sessions'] }}</div>
                <div class="stat-label">Total Sesi</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #166534;">{{ $stats['hadir'] }}</div>
                <div class="stat-label">Hadir</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #991b1b;">{{ $stats['alpa'] }}</div>
                <div class="stat-label">Alpa</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #1e40af;">{{ $stats['izin'] }}</div>
                <div class="stat-label">Izin</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #92400e;">{{ $stats['sakit'] }}</div>
                <div class="stat-label">Sakit</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #1e3a8a;">{{ $stats['attendance_rate'] }}%</div>
                <div class="stat-label">Persentase Kehadiran</div>
            </div>
        </div>
    </div>

    @if($attendances->count() > 0)
    <div class="section">
        <div class="section-title">Detail Kehadiran</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 25%;">Kelas</th>
                    <th style="width: 15%;">Hari</th>
                    <th style="width: 15%;">Status</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $index => $attendance)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}</td>
                    <td>{{ $attendance->trainingSchedule->class_name ?? '-' }} ({{ $attendance->trainingSchedule->branch ?? '-' }})</td>
                    <td>{{ $attendance->trainingSchedule->day?->label() ?? $attendance->trainingSchedule->day_of_week }}</td>
                    <td>
                        <span class="badge badge-{{ $attendance->status }}">{{ $attendance->status_label }}</span>
                    </td>
                    <td>{{ $attendance->notes ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($performances->count() > 0)
    <div class="section">
        <div class="section-title">Data Performa Fisik</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 11%;">TB / BB</th>
                    <th style="width: 10%;">VO2max</th>
                    <th style="width: 10%;">Push-up</th>
                    <th style="width: 10%;">Sit-up</th>
                    <th style="width: 11%;">Kelentukan</th>
                    <th style="width: 11%;">Agility</th>
                    <th>Stabilitas Inti</th>
                </tr>
            </thead>
            <tbody>
                @foreach($performances as $index => $performance)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $performance->created_at->format('d/m/Y') }}</td>
                    <td>
                        {{ $performance->height !== null ? number_format((float) $performance->height, 0, ',', '.') : '-' }}
                        /
                        {{ $performance->weight !== null ? number_format((float) $performance->weight, 0, ',', '.') : '-' }}
                    </td>
                    <td>{{ $performance->beep_test_vo2max !== null ? number_format((float) $performance->beep_test_vo2max, 1, ',', '.') : '-' }}</td>
                    <td>{{ $performance->push_up_1_min ?? '-' }}</td>
                    <td>{{ $performance->sit_up_2_min ?? '-' }}</td>
                    <td>{{ $performance->sit_and_reach !== null ? number_format((float) $performance->sit_and_reach, 1, ',', '.') : '-' }}</td>
                    <td>{{ $performance->illinois_agility !== null ? number_format((float) $performance->illinois_agility, 2, ',', '.') : '-' }}</td>
                    <td>{{ $performance->core_stability_level ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p style="font-size: 8px; color: #666;">
            TB/BB: tinggi (cm) / berat (kg); VO2max: beep test (ml/kg/min); Push-up: repetisi/menit;
            Sit-up: repetisi/2 menit; Kelentukan: sit &amp; reach (cm); Agility: Illinois test (detik).
        </p>

        @if(!empty($fitness_analysis))
        <div class="stats-grid" style="margin-top: 10px;">
            <div class="stat-card">
                <div class="stat-value">{{ $fitness_analysis['overall_score'] }}%</div>
                <div class="stat-label">Skor Kebugaran (REMAJA)</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="font-size: 16px; color: {{ $fitness_analysis['score_category']['color'] ?? '#1e3a8a' }};">{{ $fitness_analysis['score_category']['label'] }}</div>
                <div class="stat-label">{{ $fitness_analysis['score_category']['description'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="font-size: 16px;">{{ $fitness_analysis['bmi'] ?? '-' }} ({{ $fitness_analysis['bmi_category'] }})</div>
                <div class="stat-label">Indeks Massa Tubuh (IMT)</div>
            </div>
        </div>
        @endif
    </div>
    @endif

    @if(!empty($achievements) && $achievements->count() > 0)
    <div class="section">
        <div class="section-title">Prestasi &amp; Kejuaraan</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 30%;">Nama Kejuaraan</th>
                    <th style="width: 15%;">Tingkat</th>
                    <th style="width: 20%;">Kategori / Kelas</th>
                    <th>Medali</th>
                </tr>
            </thead>
            <tbody>
                @foreach($achievements as $index => $achievement)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($achievement->event_date)->format('d/m/Y') }}</td>
                    <td>{{ $achievement->competition_name }}</td>
                    <td>{{ $achievement->level_label }}</td>
                    <td>{{ trim(($achievement->category ?? '') . ($achievement->weight_class ? ' • ' . $achievement->weight_class : '')) ?: '-' }}</td>
                    <td><strong>{{ $achievement->medal_label }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($promotions->count() > 0)
    <div class="section">
        <div class="section-title">Riwayat UKT (Ujian Kenaikan Tingkat)</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">Tanggal UKT</th>
                    <th style="width: 15%;">Sabuk Saat Ini</th>
                    <th style="width: 15%;">Sabuk Target</th>
                    <th style="width: 15%;">Hasil</th>
                    <th style="width: 15%;">Penguji</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($promotions as $index => $promotion)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($promotion->exam_date)->format('d/m/Y') }}</td>
                    <td>{{ $promotion->current_belt_label }}</td>
                    <td>{{ $promotion->target_belt_label }}</td>
                    <td>
                        <span class="badge badge-{{ $promotion->result->value }}">{{ $promotion->result_label }}</span>
                    </td>
                    <td>{{ $promotion->examiner->name ?? '-' }}</td>
                    <td>{{ $promotion->examiner_notes ?? $promotion->notes ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>Dicetak pada: {{ $generated_at->format('d F Y H:i') }} WIB</p>
        <p>Tapak Suci Sport Academy - Sistem Informasi Akademi</p>
    </div>

    <div class="signature">
        <div class="sig-block">
            <div class="sig-line">Pelatih</div>
        </div>
        <div class="sig-block">
            <div class="sig-line">Kepala Cabang</div>
        </div>
        <div class="sig-block">
            <div class="sig-line">Orang Tua / Wali</div>
        </div>
    </div>
</body>
</html>