<?php

namespace App\Filament\Resources\Attendances\Schemas;

use App\Models\Student;
use App\Models\TrainingSchedule;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('training_schedule_id')
                    ->label('Jadwal Latihan')
                    ->options(function () {
                        $query = TrainingSchedule::query();

                        if (auth()->user()?->isPelatih()) {
                            $query->where('coach_id', auth()->id());
                        }

                        return $query->orderBy('class_name')->pluck('class_name', 'id')->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('student_id', null)),

                Select::make('student_id')
                    ->label('Siswa')
                    ->options(function (callable $get) {
                        $scheduleId = $get('training_schedule_id');
                        if (! $scheduleId) {
                            return [];
                        }
                        $schedule = TrainingSchedule::find($scheduleId);
                        if (! $schedule) {
                            return [];
                        }

                        // Belum ada tabel enrollment; fallback ke siswa di cabang jadwal.
                        return Student::query()
                            ->where('branch', $schedule->branch)
                            ->orderBy('full_name')
                            ->pluck('full_name', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->required(),

                DatePicker::make('date')
                    ->label('Tanggal')
                    ->required()
                    ->maxDate(now())
                    ->displayFormat('d/m/Y')
                    ->native(false),

                ToggleButtons::make('status')
                    ->label('Status Kehadiran')
                    ->options([
                        'hadir' => 'Hadir',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'alpa' => 'Alpa',
                    ])
                    ->default('hadir')
                    ->required()
                    ->inline()
                    ->colors([
                        'hadir' => 'success',
                        'izin' => 'info',
                        'sakit' => 'warning',
                        'alpa' => 'danger',
                    ])
                    ->icons([
                        'hadir' => 'heroicon-o-check-circle',
                        'izin' => 'heroicon-o-clock',
                        'sakit' => 'heroicon-o-face-frown',
                        'alpa' => 'heroicon-o-x-circle',
                    ]),

                Textarea::make('notes')
                    ->label('Catatan')
                    ->rows(2)
                    ->placeholder('Catatan tambahan dari pelatih...')
                    ->columnSpanFull(),
            ]);
    }
}
