<?php

namespace App\Filament\Resources\TrainingSchedules\Pages;

use App\Filament\Exports\TrainingScheduleExporter;
use App\Filament\Imports\TrainingScheduleImporter;
use App\Filament\Resources\TrainingSchedules\TrainingScheduleResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListTrainingSchedules extends ListRecords
{
    protected static string $resource = TrainingScheduleResource::class;

    public function getTitle(): string
    {
        return 'Jadwal Latihan';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah Jadwal'),
            ImportAction::make()
                ->importer(TrainingScheduleImporter::class),
            ExportAction::make()
                ->exporter(TrainingScheduleExporter::class),
        ];
    }
}
