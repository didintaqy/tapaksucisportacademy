<?php

namespace App\Filament\Resources\Achievements\Schemas;

use App\Enums\CompetitionLevel;
use App\Enums\MedalType;
use App\Models\Student;
use App\Models\TrainingSchedule;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AchievementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_id')
                    ->label('Atlet / Siswa (Kosongkan untuk Prestasi Kontingen/Akademi)')
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
                    ->nullable(),

                TextInput::make('competition_name')
                    ->label('Nama Kejuaraan')
                    ->required()
                    ->maxLength(255),

                Select::make('level')
                    ->label('Tingkat Kejuaraan')
                    ->options(CompetitionLevel::class)
                    ->default(CompetitionLevel::Kota)
                    ->required(),

                DatePicker::make('event_date')
                    ->label('Tanggal Kejuaraan')
                    ->required()
                    ->maxDate(now()->addDays(1))
                    ->displayFormat('d/m/Y')
                    ->native(false),

                TextInput::make('location')
                    ->label('Tempat / Lokasi')
                    ->maxLength(255),

                TextInput::make('organizer')
                    ->label('Penyelenggara')
                    ->maxLength(255),

                TextInput::make('category')
                    ->label('Kategori (Tanding / Seni / Usia)')
                    ->placeholder('Contoh: Tanding Pra Remaja Putra / Kontingen Usia Dini')
                    ->maxLength(255),

                TextInput::make('weight_class')
                    ->label('Kelas / Nomor')
                    ->placeholder('Contoh: Kelas D / Tunggal Baku')
                    ->maxLength(255),

                Select::make('medal')
                    ->label('Medali / Capaian')
                    ->options(MedalType::class)
                    ->default(MedalType::Emas)
                    ->required(),

                FileUpload::make('certificate_file')
                    ->label('Bukti / Piagam / Foto')
                    ->disk('public')
                    ->directory('achievements')
                    ->visibility('public')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
                    ->maxSize(5120)
                    ->helperText('Format JPG, PNG, WEBP, atau PDF. Maksimal 5 MB.'),

                Toggle::make('is_published')
                    ->label('Tampilkan di Halaman Publik')
                    ->default(true),

                Textarea::make('notes')
                    ->label('Catatan')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
