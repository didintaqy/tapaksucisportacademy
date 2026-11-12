<?php

namespace App\Filament\Resources\Galleries\Pages;

use App\Filament\Exports\GalleryExporter;
use App\Filament\Imports\GalleryImporter;
use App\Filament\Resources\Galleries\GalleryResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListGalleries extends ListRecords
{
    protected static string $resource = GalleryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(GalleryImporter::class),
            ExportAction::make()
                ->exporter(GalleryExporter::class),
        ];
    }
}
