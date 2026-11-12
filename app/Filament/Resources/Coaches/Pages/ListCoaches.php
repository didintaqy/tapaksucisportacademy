<?php

namespace App\Filament\Resources\Coaches\Pages;

use App\Filament\Exports\CoachExporter;
use App\Filament\Imports\CoachImporter;
use App\Filament\Resources\Coaches\CoachResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListCoaches extends ListRecords
{
    protected static string $resource = CoachResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(CoachImporter::class),
            ExportAction::make()
                ->exporter(CoachExporter::class),
        ];
    }
}
