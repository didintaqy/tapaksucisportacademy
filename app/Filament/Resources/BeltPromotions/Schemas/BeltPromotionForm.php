<?php

namespace App\Filament\Resources\BeltPromotions\Schemas;

use App\Enums\BeltRank;
use App\Models\Student;
use App\Models\TrainingSchedule;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Schemas\Schema;

class BeltPromotionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_id')
                    ->label('Siswa')
                    ->options(function () {
                        $query = Student::query();

                        if (auth()->user()?->isPelatih()) {
                            $branches = TrainingSchedule::query()
                                ->where('coach_id', auth()->id())
                                ->whereNotNull('branch')
                                ->pluck('branch')
                                ->unique();

                            $query->whereIn('branch', $branches);
                        }

                        return $query->orderBy('full_name')->pluck('full_name', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Get $get, Set $set) => $set('current_belt', Student::find($get('student_id'))?->belt_level ?? '')),

                Select::make('current_belt')
                    ->label('Sabuk Saat Ini')
                    ->options(BeltRank::class)
                    ->required()
                    ->disabled()
                    ->dehydrated(),

                Select::make('target_belt')
                    ->label('Sabuk Target')
                    ->options(fn (Get $get) => self::getNextBelts($get('current_belt')))
                    ->required()
                    ->searchable()
                    ->preload(),

                DatePicker::make('exam_date')
                    ->label('Tanggal UKT')
                    ->required()
                    ->maxDate(now())
                    ->displayFormat('d/m/Y')
                    ->native(false),

                Select::make('examined_by')
                    ->label('Penguji')
                    ->options(fn () => User::where('role', 'pelatih')->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),

                ToggleButtons::make('result')
                    ->label('Hasil UKT')
                    ->options([
                        'lulus' => 'Lulus',
                        'tidak_lulus' => 'Tidak Lulus',
                        'ditunda' => 'Ditunda',
                    ])
                    ->default('ditunda')
                    ->required()
                    ->inline()
                    ->colors([
                        'lulus' => 'success',
                        'tidak_lulus' => 'danger',
                        'ditunda' => 'warning',
                    ])
                    ->icons([
                        'lulus' => 'heroicon-o-check-circle',
                        'tidak_lulus' => 'heroicon-o-x-circle',
                        'ditunda' => 'heroicon-o-clock',
                    ])
                    ->live()
                    ->afterStateUpdated(fn (Set $set, $state) => $state === 'lulus' ? $set('certificate_issued_at', now()) : $set('certificate_issued_at', null)),

                Textarea::make('notes')
                    ->label('Catatan Siswa')
                    ->rows(2)
                    ->placeholder('Catatan persiapan, prestasi, dll...')
                    ->columnSpanFull(),

                Textarea::make('examiner_notes')
                    ->label('Catatan Penguji')
                    ->rows(2)
                    ->placeholder('Catatan dari penguji saat UKT...')
                    ->columnSpanFull(),

                DatePicker::make('certificate_issued_at')
                    ->label('Tanggal Terbit Sertifikat')
                    ->displayFormat('d/m/Y')
                    ->native(false)
                    ->visible(fn (Get $get) => $get('result') === 'lulus'),

                Select::make('certificate_number')
                    ->label('Nomor Sertifikat')
                    ->options(fn () => ['auto' => 'Otomatis (UKT-YYYY-NNNN)'])
                    ->default('auto')
                    ->disabled()
                    ->dehydrated(false)
                    ->visible(fn (Get $get) => $get('result') === 'lulus')
                    ->helperText('Nomor sertifikat akan digenerate otomatis saat disimpan'),
            ]);
    }

    private static function getNextBelts(?string $currentBelt): array
    {
        $belts = [
            'putih' => 'Putih',
            'kuning' => 'Kuning',
            'hijau' => 'Hijau',
            'biru' => 'Biru',
            'merah' => 'Merah',
            'coklat' => 'Coklat',
            'hitam_1' => 'Hitam 1 Dan',
            'hitam_2' => 'Hitam 2 Dan',
            'hitam_3' => 'Hitam 3 Dan',
            'hitam_4' => 'Hitam 4 Dan',
            'hitam_5' => 'Hitam 5 Dan',
        ];

        if (blank($currentBelt)) {
            return [];
        }

        $currentIndex = array_search($currentBelt, array_keys($belts), true);
        if ($currentIndex === false || $currentIndex === count($belts) - 1) {
            return [$currentBelt => $belts[$currentBelt] ?? $currentBelt];
        }

        return array_slice($belts, $currentIndex + 1, 1, true);
    }
}
