<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Exports\EventExporter;
use App\Filament\Imports\EventImporter;
use App\Filament\Resources\Events\EventResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListEvents extends ListRecords
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(EventImporter::class),
            ExportAction::make()
                ->exporter(EventExporter::class),
        ];
    }
}
