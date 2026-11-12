<?php

namespace App\Filament\Resources\FitnessBenchmarks\Pages;

use App\Filament\Resources\FitnessBenchmarks\FitnessBenchmarkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFitnessBenchmarks extends ListRecords
{
    protected static string $resource = FitnessBenchmarkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
