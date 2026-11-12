<?php

namespace App\Filament\Resources\Attendances\Pages;

use App\Filament\Exports\AttendanceExporter;
use App\Filament\Imports\AttendanceImporter;
use App\Filament\Resources\Attendances\AttendanceResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(AttendanceImporter::class),
            ExportAction::make()
                ->exporter(AttendanceExporter::class),
        ];
    }
}
