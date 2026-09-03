<?php

namespace App\Filament\Resources\StudentPerformances\Pages;

use App\Filament\Resources\StudentPerformances\StudentPerformanceResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditStudentPerformance extends EditRecord
{
    protected static string $resource = StudentPerformanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
