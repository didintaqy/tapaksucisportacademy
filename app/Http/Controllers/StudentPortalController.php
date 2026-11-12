<?php

namespace App\Http\Controllers;

use App\Enums\BeltRank;
use App\Enums\MedalType;
use App\Enums\PromotionResult;
use App\Models\BeltPromotion;
use App\Models\Student;
use App\Services\Fitness\PerformanceAnalysisService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentPortalController extends Controller
{
    public function __construct(
        private readonly PerformanceAnalysisService $fitnessService,
    ) {}

    public function index(): View|RedirectResponse
    {
        $student = $this->currentStudent();
        if ($student) {
            return redirect()->route('student.portal.dashboard');
        }

        return view('frontend.student-portal.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'string', 'max:32'],
            'birth_date' => ['required', 'date', 'before:today'],
        ], [], [
            'student_id' => 'Kode Siswa / NIK',
            'birth_date' => 'Tanggal Lahir',
        ]);

        $keyword = trim($data['student_id']);

        $student = Student::query()
            ->where(function ($query) use ($keyword): void {
                $query->where('id', $keyword)
                    ->orWhere('student_code', $keyword)
                    ->orWhere('nik', $keyword);
            })
            ->first();

        $valid = $student
            && $student->birth_date
            && Carbon::parse($student->birth_date)->isSameDay($data['birth_date']);

        if (! $valid) {
            return back()
                ->withInput()
                ->with('error', 'Data siswa tidak ditemukan. Periksa Kode Siswa/NIK dan tanggal lahir.');
        }

        session([
            'student_portal_student_id' => $student->id,
            'payment_portal_student_id' => $student->id,
        ]);

        return redirect()->route('student.portal.dashboard');
    }

    public function dashboard(): View|RedirectResponse
    {
        $student = $this->currentStudent();

        if (! $student) {
            return redirect()
                ->route('student.portal')
                ->with('error', 'Silakan masuk menggunakan Kode Siswa/NIK dan tanggal lahir terlebih dahulu.');
        }

        $student->load(['program']);

        $progression = $this->fitnessService->analyzeProgression($student);
        $latestSession = ! empty($progression['sessions'])
            ? $progression['sessions'][count($progression['sessions']) - 1]
            : null;

        $achievements = $student->achievements()
            ->orderByDesc('event_date')
            ->get();

        $medalCounts = [
            'emas' => $achievements->where('medal', MedalType::Emas)->count(),
            'perak' => $achievements->where('medal', MedalType::Perak)->count(),
            'perunggu' => $achievements->where('medal', MedalType::Perunggu)->count(),
            'total' => $achievements->count(),
        ];

        $attendances = $student->attendances()
            ->with('trainingSchedule')
            ->orderByDesc('date')
            ->take(20)
            ->get();

        $allAttendanceCounts = $student->attendances()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalSessions = (int) $allAttendanceCounts->sum();
        $hadirCount = (int) ($allAttendanceCounts['hadir'] ?? 0);
        $attendanceStats = [
            'total' => $totalSessions,
            'hadir' => $hadirCount,
            'izin' => (int) ($allAttendanceCounts['izin'] ?? 0),
            'sakit' => (int) ($allAttendanceCounts['sakit'] ?? 0),
            'alpa' => (int) ($allAttendanceCounts['alpa'] ?? 0),
            'rate' => $totalSessions > 0 ? round(($hadirCount / $totalSessions) * 100, 1) : 0.0,
        ];

        $beltPromotions = $student->beltPromotions()
            ->with('examiner')
            ->orderByDesc('exam_date')
            ->get();

        $payments = $student->payments()
            ->with('paymentType')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        $currentBelt = BeltRank::tryFrom((string) $student->belt_level)?->label() ?? ($student->belt_level ?: '-');

        return view('frontend.student-portal.dashboard', [
            'student' => $student,
            'currentBelt' => $currentBelt,
            'latestAnalysis' => $latestSession['analysis'] ?? null,
            'progression' => $progression,
            'achievements' => $achievements,
            'medalCounts' => $medalCounts,
            'attendances' => $attendances,
            'attendanceStats' => $attendanceStats,
            'beltPromotions' => $beltPromotions,
            'payments' => $payments,
        ]);
    }

    public function downloadReportPdf(Request $request): StreamedResponse|RedirectResponse
    {
        $student = $this->currentStudent();

        if (! $student) {
            return redirect()
                ->route('student.portal')
                ->with('error', 'Silakan masuk terlebih dahulu.');
        }

        $dateFrom = $request->query('from', now()->subYear()->toDateString());
        $dateTo = $request->query('to', now()->toDateString());

        $attendances = $student->attendances()
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->with('trainingSchedule')
            ->orderBy('date')
            ->get();

        $performances = $student->performances()
            ->orderBy('created_at')
            ->get();

        $promotions = $student->beltPromotions()
            ->with('examiner')
            ->orderBy('exam_date')
            ->get();

        $achievements = $student->achievements()
            ->orderBy('event_date')
            ->get();

        $latestPerformance = $performances->last();
        $fitnessAnalysis = $latestPerformance
            ? $this->fitnessService->analyze($latestPerformance, $student)
            : null;

        $totalSessions = $attendances->count();
        $hadirCount = $attendances->where('status', 'hadir')->count();
        $currentBelt = BeltRank::tryFrom((string) $student->belt_level)?->label() ?? $student->belt_level;

        $html = Blade::render('filament.pages.reports.student-report', [
            'student' => $student,
            'current_belt' => $currentBelt,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'generated_at' => now(),
            'attendances' => $attendances,
            'performances' => $performances,
            'promotions' => $promotions,
            'achievements' => $achievements,
            'fitness_analysis' => $fitnessAnalysis,
            'stats' => [
                'total_sessions' => $totalSessions,
                'hadir' => $hadirCount,
                'izin' => $attendances->where('status', 'izin')->count(),
                'sakit' => $attendances->where('status', 'sakit')->count(),
                'alpa' => $attendances->where('status', 'alpa')->count(),
                'attendance_rate' => $totalSessions > 0 ? round(($hadirCount / $totalSessions) * 100, 1) : 0,
            ],
        ]);

        $pdf = Pdf::loadHTML($html)
            ->setPaper('A4', 'portrait')
            ->setWarnings(false);

        $filename = 'rapor-'.($student->student_code ?: $student->id).'-'.now()->format('Ymd').'.pdf';

        return response()->streamDownload(
            function () use ($pdf): void {
                echo $pdf->output();
            },
            $filename,
            ['Content-Type' => 'application/pdf'],
        );
    }

    public function certificate(BeltPromotion $beltPromotion): View
    {
        $student = $this->currentStudent();

        abort_unless($student && $beltPromotion->student_id === $student->id, 404);
        abort_unless(
            $beltPromotion->result === PromotionResult::Lulus && filled($beltPromotion->certificate_number),
            403,
            'Sertifikat belum tersedia untuk ujian ini.',
        );

        $beltPromotion->load(['student', 'examiner']);

        return view('certificates.belt-promotion', [
            'promotion' => $beltPromotion,
        ]);
    }

    public function logout(): RedirectResponse
    {
        session()->forget('student_portal_student_id');

        return redirect()->route('student.portal');
    }

    protected function currentStudent(): ?Student
    {
        $id = session('student_portal_student_id') ?? session('payment_portal_student_id');

        return $id ? Student::find($id) : null;
    }
}
