<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Exports\StudentExporter;
use App\Filament\Imports\StudentImporter;
use App\Filament\Resources\Students\StudentResource;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ImportAction::make()
                ->importer(StudentImporter::class),
            ExportAction::make()
                ->exporter(StudentExporter::class),
        ];
    }
}
