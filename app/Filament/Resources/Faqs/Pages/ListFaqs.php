<?php

namespace App\Filament\Resources\Faqs\Pages;

use App\Filament\Exports\FaqExporter;
use App\Filament\Imports\FaqImporter;
use App\Filament\Resources\Faqs\FaqResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListFaqs extends ListRecords
{
    protected static string $resource = FaqResource::class;

    public function getTitle(): string
    {
        return 'FAQ';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah FAQ'),
            ImportAction::make()
                ->importer(FaqImporter::class),
            ExportAction::make()
                ->exporter(FaqExporter::class),
        ];
    }
}
