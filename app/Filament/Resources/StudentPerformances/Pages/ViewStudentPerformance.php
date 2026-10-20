<?php

namespace App\Filament\Resources\StudentPerformances\Pages;

use App\Filament\Resources\StudentPerformances\StudentPerformanceResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStudentPerformance extends ViewRecord
{
    protected static string $resource = StudentPerformanceResource::class;

    public function getTitle(): string
    {
        return 'Performance Analysis: '.($this->record->student?->nickname ?? 'Siswa');
    }

    protected function getHeaderWidgets(): array
    {
        return [

        ];
    }

    protected function getHeaderActions(): array
    {
        $currentRecord = $this->record;

        // Cari riwayat performa sebelumnya (tes yang lebih lama) untuk siswa yang sama
        $previousRecord = static::getResource()::getModel()::where('student_id', $currentRecord->student_id)
            ->where('id', '<', $currentRecord->id)
            ->orderBy('id', 'desc')
            ->first();

        // Cari riwayat performa berikutnya (tes yang lebih baru) untuk siswa yang sama
        $nextRecord = static::getResource()::getModel()::where('student_id', $currentRecord->student_id)
            ->where('id', '>', $currentRecord->id)
            ->orderBy('id', 'asc')
            ->first();

        return [
            // Tombol Previous
            Action::make('previous')
                ->label('Prev')
                ->icon('heroicon-m-chevron-left')
                ->color('gray')
                ->disabled(! $previousRecord)
                ->url($previousRecord ? static::getResource()::getUrl('view', ['record' => $previousRecord->id]) : null),

            // Tombol Next
            Action::make('next')
                ->label('Next')
                ->icon('heroicon-m-chevron-right')
                ->iconPosition('after')
                ->color('gray')
                ->disabled(! $nextRecord)
                ->url($nextRecord ? static::getResource()::getUrl('view', ['record' => $nextRecord->id]) : null),

            // Tombol Print
            Action::make('print')
                ->label('Cetak')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->extraAttributes(['onclick' => 'window.print()']),

            // Tombol Edit
            EditAction::make(),
        ];
    }
}
