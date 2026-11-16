<?php

namespace App\Filament\Resources\FitnessBenchmarks\Pages;

use App\Filament\Exports\FitnessBenchmarkExporter;
use App\Filament\Imports\FitnessBenchmarkImporter;
use App\Filament\Resources\FitnessBenchmarks\FitnessBenchmarkResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListFitnessBenchmarks extends ListRecords
{
    protected static string $resource = FitnessBenchmarkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(FitnessBenchmarkImporter::class),
            ExportAction::make()
                ->exporter(FitnessBenchmarkExporter::class),
        ];
    }
}
