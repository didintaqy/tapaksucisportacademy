<?php

namespace App\Filament\Pages;

use App\Enums\BeltRank;
use App\Models\Attendance;
use App\Models\BeltPromotion;
use App\Models\Student;
use App\Models\StudentPerformance;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Blade;
use UnitEnum;

class GenerateReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    protected static UnitEnum|string|null $navigationGroup = 'Manajemen Akademi';

    protected static ?string $navigationLabel = 'Rapor Performa';

    protected static ?string $title = 'Generate Rapor Performa Siswa';

    protected string $view = 'filament.pages.generate-report';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'date_from' => now()->startOfMonth()->format('Y-m-d'),
            'date_to' => now()->endOfMonth()->format('Y-m-d'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('student_id')
                    ->label('Siswa')
                    ->options(fn () => Student::orderBy('full_name')->pluck('full_name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live(),

                DatePicker::make('date_from')
                    ->label('Dari Tanggal')
                    ->required()
                    ->maxDate(now())
                    ->displayFormat('d/m/Y')
                    ->native(false),

                DatePicker::make('date_to')
                    ->label('Sampai Tanggal')
                    ->required()
                    ->maxDate(now())
                    ->displayFormat('d/m/Y')
                    ->native(false)
                    ->after('date_from'),

                Select::make('format')
                    ->label('Format Output')
                    ->options([
                        'pdf' => 'PDF (DomPDF)',
                        'html' => 'HTML (Preview)',
                    ])
                    ->default('pdf')
                    ->required(),
            ])
            ->statePath('data');
    }

    public function generateReport(): void
    {
        $data = $this->form->getState();

        $student = Student::with(['performances', 'payments', 'beltPromotions'])->find($data['student_id']);

        if (! $student) {
            Notification::make()
                ->title('Siswa tidak ditemukan')
                ->danger()
                ->send();

            return;
        }

        $dateFrom = $data['date_from'];
        $dateTo = $data['date_to'];

        $attendances = Attendance::where('student_id', $student->id)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->with('trainingSchedule')
            ->orderBy('date')
            ->get();

        $performances = StudentPerformance::where('student_id', $student->id)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->orderBy('date')
            ->get();

        $promotions = BeltPromotion::where('student_id', $student->id)
            ->whereBetween('exam_date', [$dateFrom, $dateTo])
            ->with('examiner')
            ->orderBy('exam_date')
            ->get();

        $totalSessions = $attendances->count();
        $hadirCount = $attendances->where('status', 'hadir')->count();
        $izinCount = $attendances->where('status', 'izin')->count();
        $sakitCount = $attendances->where('status', 'sakit')->count();
        $alpaCount = $attendances->where('status', 'alpa')->count();
        $attendanceRate = $totalSessions > 0 ? round(($hadirCount / $totalSessions) * 100, 1) : 0;

        $currentBelt = BeltRank::tryFrom($student->belt_level)?->label() ?? $student->belt_level;

        $reportData = [
            'student' => $student,
            'current_belt' => $currentBelt,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'generated_at' => now(),
            'attendances' => $attendances,
            'performances' => $performances,
            'promotions' => $promotions,
            'stats' => [
                'total_sessions' => $totalSessions,
                'hadir' => $hadirCount,
                'izin' => $izinCount,
                'sakit' => $sakitCount,
                'alpa' => $alpaCount,
                'attendance_rate' => $attendanceRate,
            ],
        ];

        $html = Blade::render('filament.pages.reports.student-report', $reportData);

        if ($data['format'] === 'pdf') {
            $pdf = Pdf::loadHTML($html)
                ->setPaper('A4', 'portrait')
                ->setWarnings(false);

            $filename = 'rapor-'.$student->student_code.'-'.now()->format('Ymd').'.pdf';

            $this->streamDownload($pdf->stream(), $filename);
        } else {
            $this->dispatch('showHtmlPreview', html: $html);
        }
    }
}
