<?php

namespace App\Filament\Resources\CoachRates\Pages;

use App\Filament\Resources\CoachRates\CoachRateResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCoachRate extends EditRecord
{
    protected static string $resource = CoachRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
