<?php

namespace App\Filament\Resources\CoachPayrolls\Pages;

use App\Filament\Resources\CoachPayrolls\CoachPayrollResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCoachPayrolls extends ListRecords
{
    protected static string $resource = CoachPayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
