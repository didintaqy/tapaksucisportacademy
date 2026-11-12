<?php

namespace App\Filament\Resources\FitnessBenchmarks\Pages;

use App\Filament\Resources\FitnessBenchmarks\FitnessBenchmarkResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFitnessBenchmark extends EditRecord
{
    protected static string $resource = FitnessBenchmarkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
