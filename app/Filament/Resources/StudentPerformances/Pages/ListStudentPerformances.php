<?php

namespace App\Filament\Resources\StudentPerformances\Pages;

use App\Filament\Resources\StudentPerformances\StudentPerformanceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStudentPerformances extends ListRecords
{
    protected static string $resource = StudentPerformanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
