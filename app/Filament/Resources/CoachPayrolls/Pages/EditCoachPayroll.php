<?php

namespace App\Filament\Resources\CoachPayrolls\Pages;

use App\Filament\Resources\CoachPayrolls\CoachPayrollResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCoachPayroll extends EditRecord
{
    protected static string $resource = CoachPayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
