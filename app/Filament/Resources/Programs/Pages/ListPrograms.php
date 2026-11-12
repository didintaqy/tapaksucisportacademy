<?php

namespace App\Filament\Resources\Programs\Pages;

use App\Filament\Exports\ProgramExporter;
use App\Filament\Imports\ProgramImporter;
use App\Filament\Resources\Programs\ProgramResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListPrograms extends ListRecords
{
    protected static string $resource = ProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(ProgramImporter::class),
            ExportAction::make()
                ->exporter(ProgramExporter::class),
        ];
    }
}
