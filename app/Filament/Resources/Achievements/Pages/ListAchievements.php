<?php

namespace App\Filament\Resources\Achievements\Pages;

use App\Filament\Exports\AchievementExporter;
use App\Filament\Imports\AchievementImporter;
use App\Filament\Resources\Achievements\AchievementResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListAchievements extends ListRecords
{
    protected static string $resource = AchievementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(AchievementImporter::class),
            ExportAction::make()
                ->exporter(AchievementExporter::class),
        ];
    }
}
