<?php

namespace App\Filament\Resources\CoachRates\Pages;

use App\Filament\Resources\CoachRates\CoachRateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCoachRates extends ListRecords
{
    protected static string $resource = CoachRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
