<?php

namespace App\Filament\Resources\StudentPerformances\Pages;

use App\Filament\Resources\StudentPerformances\StudentPerformanceResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStudentPerformance extends ViewRecord
{
    protected static string $resource = StudentPerformanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
