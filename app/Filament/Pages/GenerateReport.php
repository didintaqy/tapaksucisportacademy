<?php

namespace App\Filament\Pages;

use App\Enums\BeltRank;
use App\Enums\UserRole;
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
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

class GenerateReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    protected static UnitEnum|string|null $navigationGroup = 'Manajemen Akademi';

    protected static ?string $navigationLabel = 'Rapor Performa';

    protected static ?string $title = 'Generate Rapor Performa Siswa';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.generate-report';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(UserRole::Admin, UserRole::Operator) ?? false;
    }

    public function mount(): void
    {
        $this->form->fill([
            'date_from' => now()->startOfMonth()->format('Y-m-d'),
            'date_to' => now()->endOfMonth()->format('Y-m-d'),
            'format' => 'pdf',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_id')
                    ->label('Siswa')
                    ->options(fn () => Student::orderBy('full_name')->pluck('full_name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),

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

    public function generateReport(): StreamedResponse|Response|null
    {
        $data = $this->form->getState();

        $student = Student::find($data['student_id']);

        if (! $student) {
            Notification::make()
                ->title('Siswa tidak ditemukan')
                ->danger()
                ->send();

            return null;
        }

        $dateFrom = Carbon::parse($data['date_from'])->toDateString();
        $dateTo = Carbon::parse($data['date_to'])->toDateString();

        $attendances = Attendance::where('student_id', $student->id)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->with('trainingSchedule')
            ->orderBy('date')
            ->get();

        $performances = StudentPerformance::where('student_id', $student->id)
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->orderBy('created_at')
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
            'stats' => [
                'total_sessions' => $totalSessions,
                'hadir' => $hadirCount,
                'izin' => $izinCount,
                'sakit' => $sakitCount,
                'alpa' => $alpaCount,
                'attendance_rate' => $attendanceRate,
            ],
        ]);

        if ($data['format'] === 'pdf') {
            $pdf = Pdf::loadHTML($html)
                ->setPaper('A4', 'portrait')
                ->setWarnings(false);

            $filename = 'rapor-'.$student->student_code.'-'.now()->format('Ymd').'.pdf';

            return response()->streamDownload(
                function () use ($pdf): void {
                    echo $pdf->output();
                },
                $filename,
                ['Content-Type' => 'application/pdf'],
            );
        }

        $this->dispatch('showHtmlPreview', html: $html);

        return null;
    }
}
