<?php

namespace App\Filament\Resources\Students\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Student;
use Filament\Support\Enums\IconPosition;
use Carbon\Carbon;

class StudentOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        // 1. Data Gender & Total
        $male = Student::where('gender', 'L')->count();
        $female = Student::where('gender', 'P')->count();
        $total = $male + $female;

        $dominantGender = $male >= $female ? 'Laki-laki' : 'Perempuan';
        $dominantPercent = $total > 0 ? round((max($male, $female) / $total) * 100) : 0;

        // 2. Data Jenjang Pendidikan
        $sdCount = Student::where('education', 'SD')->count();
        $smpCount = Student::where('education', 'SMP')->count();
        $smaCount = Student::where('education', 'SMA')->count();

        // 3. Data Tingkatan Sabuk (Kuning Dasar vs Melati)
        $dasarCount = Student::where('belt_level', 'LIKE', '%DASAR%')->count();
        $melatiCount = Student::where('belt_level', 'LIKE', '%MELATI%')->count();
        $topBelt = Student::where('belt_level', 'LIKE', '%MELATI%')
            ->orderBy('belt_level', 'desc')
            ->value('belt_level') ?? 'KUNING DASAR';

        // 4. Data Rata-rata Usia Siswa (Berdasarkan birth_date)
        $birthDates = Student::whereNotNull('birth_date')->pluck('birth_date');
        $avgAge = $birthDates->isNotEmpty()
            ? round($birthDates->avg(fn ($date) => Carbon::parse($date)->age))
            : 0;

        $youngestAge = $birthDates->isNotEmpty()
            ? $birthDates->min(fn ($date) => Carbon::parse($date)->age)
            : 0;
        $oldestAge = $birthDates->isNotEmpty()
            ? $birthDates->max(fn ($date) => Carbon::parse($date)->age)
            : 0;

        return [
            // Stat 1: Total & Rasio Gender (Pilihan Anda)
            Stat::make('Total Siswa', $total)
                ->description("{$dominantPercent}% {$dominantGender} ({$male}L / {$female}P)")
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                ->chart([$male, $female])
                ->color('success'),

            // Stat 2: Distribusi Pendidikan Sekolah
            Stat::make('Jenjang Sekolah', "SD: {$sdCount}")
                ->description("SMP: {$smpCount} siswa • SMA: {$smaCount} siswa")
                ->descriptionIcon('heroicon-m-academic-cap', IconPosition::Before)
                ->chart([$sdCount, $smpCount, $smaCount])
                ->color('info'),

            // Stat 3: Tingkatan Sabuk Siswa
            Stat::make('Tingkat Melati', "{$melatiCount} Siswa")
                ->description("Top: {$topBelt} ({$dasarCount} Dasar)")
                ->descriptionIcon('heroicon-m-sparkles', IconPosition::Before)
                ->chart([$dasarCount, $melatiCount])
                ->color('warning'),

            // Stat 4: Rentang & Rata-rata Usia
            Stat::make('Rata-rata Usia', "{$avgAge} Tahun")
                ->description("Rentang usia: {$youngestAge} - {$oldestAge} tahun")
                ->descriptionIcon('heroicon-m-calendar-days', IconPosition::Before)
                ->chart([$youngestAge, (int)$avgAge, $oldestAge])
                ->color('primary'),
        ];
    }
}
