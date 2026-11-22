<?php

namespace App\Filament\Imports;

use App\Enums\BeltRank;
use App\Models\Program;
use App\Models\Student;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Throwable;

class StudentImporter extends BaseImporter
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        $columns = [
            ImportColumn::make('full_name')
                ->label('Nama Lengkap')
                ->example('Ahmad Fauzi')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->helperText('Wajib diisi. Nama lengkap siswa.')
                ->castStateUsing(fn (mixed $state): ?string => self::normalizeText($state)),
            ImportColumn::make('nickname')
                ->label('Nama Panggilan')
                ->example('Ahmad')
                ->rules(['nullable', 'max:255'])
                ->castStateUsing(fn (mixed $state): ?string => self::normalizeText($state)),
            ImportColumn::make('birth_place')
                ->label('Tempat Lahir')
                ->example('Padang')
                ->rules(['nullable', 'max:255'])
                ->castStateUsing(fn (mixed $state): ?string => self::normalizeText($state)),
            ImportColumn::make('birth_date')
                ->label('Tanggal Lahir')
                ->example('2012-04-15')
                ->rules(['nullable', 'date'])
                ->helperText('Format YYYY-MM-DD (contoh: 2012-04-15). Format d/m/Y juga diterima.')
                ->castStateUsing(fn (mixed $state): ?string => self::normalizeDate($state)),
            ImportColumn::make('nik')
                ->label('NIK')
                ->example('1371011504120001')
                ->rules(['nullable', 'max:16'])
                ->helperText("16 digit. Di Excel, awali dengan apostrof (') agar angka 0 tidak hilang.")
                ->castStateUsing(fn (mixed $state): ?string => self::normalizeDigits($state)),
            ImportColumn::make('family_card_number')
                ->label('No. Kartu Keluarga')
                ->example('1371011504120002')
                ->rules(['nullable', 'max:16'])
                ->helperText("16 digit. Di Excel, awali dengan apostrof (') agar angka 0 tidak hilang.")
                ->castStateUsing(fn (mixed $state): ?string => self::normalizeDigits($state)),
            ImportColumn::make('phone_number')
                ->label('No. Telepon')
                ->example('081234567890')
                ->rules(['nullable', 'max:20'])
                ->helperText('Boleh memakai +62; otomatis dirapikan menjadi 08...')
                ->castStateUsing(fn (mixed $state): ?string => self::normalizePhone($state)),
            ImportColumn::make('email')
                ->label('Email')
                ->example('ahmad@example.com')
                ->rules(['nullable', 'email', 'max:255'])
                ->castStateUsing(fn (mixed $state): ?string => self::normalizeEmail($state)),
            ImportColumn::make('gender')
                ->label('Jenis Kelamin')
                ->examples(['L', 'P'])
                ->rules(['nullable', Rule::in(['L', 'P'])])
                ->helperText('Isi L untuk Laki-laki atau P untuk Perempuan.')
                ->castStateUsing(fn (mixed $state): ?string => self::normalizeGender($state)),
            ImportColumn::make('belt_level')
                ->label('Tingkat Sabuk')
                ->examples(['Siswa Dasar', 'Siswa Melati 1'])
                ->rules(['nullable', Rule::enum(BeltRank::class)])
                ->helperText('Isi nama sabuk, contoh: Siswa Dasar, Kader Muda, Pendekar Utama.')
                ->castStateUsing(fn (mixed $state): ?string => self::normalizeBeltLevel($state)),
            ImportColumn::make('education')
                ->label('Pendidikan')
                ->examples(['SD', 'SMP'])
                ->rules(['nullable', 'max:20'])
                ->helperText('Contoh: SD, SMP, SMA, D3, S1, S2, S3.')
                ->castStateUsing(fn (mixed $state): ?string => self::normalizeEducation($state)),
            ImportColumn::make('address')
                ->label('Alamat')
                ->example('Jl. Melati No. 10, Padang')
                ->rules(['nullable', 'max:2000'])
                ->castStateUsing(fn (mixed $state): ?string => self::normalizeAddress($state)),
            ImportColumn::make('branch')
                ->label('Cabang')
                ->example('Cabang Pusat')
                ->rules(['nullable', 'max:255'])
                ->helperText('Nama cabang. Cabang baru akan dibuat otomatis.')
                ->castStateUsing(fn (mixed $state): ?string => self::normalizeText($state)),
            ImportColumn::make('program')
                ->label('Program')
                ->examples(fn (): array => Program::query()
                    ->orderBy('name')
                    ->limit(2)
                    ->pluck('name')
                    ->all())
                ->relationship('program', function (?string $state): ?Program {
                    $state = trim((string) $state);

                    if ($state === '') {
                        return null;
                    }

                    return Program::query()
                        ->whereRaw('LOWER(name) = ?', [Str::lower($state)])
                        ->first();
                })
                ->helperText('Nama program harus sama dengan data Program Latihan.'),
        ];

        return array_map(
            fn (ImportColumn $column): ImportColumn => $column->exampleHeader($column->getLabel()),
            $columns,
        );
    }

    protected static function uniqueColumns(): ?array
    {
        return ['nik'];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Impor data siswa selesai: '.Number::format($import->successful_rows).' baris berhasil diimpor.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' baris gagal. Unduh laporan kegagalan untuk melihat detailnya.';
        }

        return $body;
    }

    private static function normalizeText(mixed $state): ?string
    {
        $value = Str::squish(trim((string) $state));

        return $value === '' ? null : $value;
    }

    private static function normalizeAddress(mixed $state): ?string
    {
        $value = trim((string) $state);

        return $value === '' ? null : $value;
    }

    private static function normalizeEmail(mixed $state): ?string
    {
        $value = Str::lower(trim((string) $state));

        return $value === '' ? null : $value;
    }

    private static function normalizeDigits(mixed $state): ?string
    {
        $value = trim((string) $state);

        // Excel kadang mengubah NIK/KK menjadi notasi ilmiah (mis. 3,17E+15).
        if (preg_match('/^\d+(?:[.,]\d+)?e\+\d+$/i', $value)) {
            $value = number_format((float) str_replace(',', '.', $value), 0, '', '');
        }

        $value = preg_replace('/\D+/', '', $value) ?? '';

        return $value === '' ? null : $value;
    }

    private static function normalizePhone(mixed $state): ?string
    {
        $value = preg_replace('/[^\d+]/', '', trim((string) $state)) ?? '';

        if (str_starts_with($value, '+62')) {
            $value = '0'.substr($value, 3);
        } elseif (str_starts_with($value, '62') && strlen($value) > 10) {
            $value = '0'.substr($value, 2);
        }

        return $value === '' ? null : $value;
    }

    private static function normalizeGender(mixed $state): ?string
    {
        $value = Str::of((string) $state)->lower()->squish()->toString();

        return match ($value) {
            '' => null,
            'l', 'lk', 'laki laki', 'laki-laki', 'laki2', 'male', 'm' => 'L',
            'p', 'pr', 'perempuan', 'female', 'f' => 'P',
            // Nilai tidak dikenal dibiarkan agar validasi menampilkan error.
            default => (string) $state,
        };
    }

    private static function normalizeBeltLevel(mixed $state): ?string
    {
        $value = trim((string) $state);

        if ($value === '') {
            return null;
        }

        $normalized = Str::of($value)->lower()->replace(['_', '-'], ' ')->squish()->toString();

        foreach (BeltRank::cases() as $rank) {
            $rankValue = Str::of($rank->value)->lower()->replace('_', ' ')->squish()->toString();
            $rankLabel = Str::of($rank->label())->lower()->squish()->toString();

            if (in_array($normalized, [$rankValue, $rankLabel], true)) {
                return $rank->value;
            }
        }

        // Nilai tidak dikenal dibiarkan agar validasi enum menampilkan error.
        return $value;
    }

    private static function normalizeEducation(mixed $state): ?string
    {
        $value = Str::upper(Str::squish((string) $state));

        if ($value === '') {
            return null;
        }

        return match ($value) {
            'SEKOLAH DASAR' => 'SD',
            'SEKOLAH MENENGAH PERTAMA' => 'SMP',
            'SEKOLAH MENENGAH ATAS', 'SLTA', 'SMK' => 'SMA',
            'DIPLOMA' => 'D3',
            'SARJANA' => 'S1',
            default => $value,
        };
    }

    private static function normalizeDate(mixed $state): ?string
    {
        $value = trim((string) $state);

        if ($value === '') {
            return null;
        }

        // Serial date Excel, misalnya 41043 untuk 2012-04-15.
        if (is_numeric($value) && (float) $value > 20000 && (float) $value < 80000) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
            } catch (Throwable) {
                // Lanjut ke percobaan format tanggal biasa.
            }
        }

        foreach (['d/m/Y', 'd-m-Y', 'd.m.Y', 'Y-m-d', 'Y/m/d', 'm/d/Y'] as $format) {
            try {
                $date = Carbon::createFromFormat('!'.$format, $value);

                if ($date instanceof Carbon) {
                    return $date->format('Y-m-d');
                }
            } catch (Throwable) {
                // Coba format berikutnya.
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (Throwable) {
            // Biarkan nilai asli agar validasi 'date' menampilkan error.
            return $value;
        }
    }
}
