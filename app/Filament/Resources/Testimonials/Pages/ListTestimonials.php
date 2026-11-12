<?php

namespace App\Filament\Resources\Testimonials\Pages;

use App\Filament\Exports\TestimonialExporter;
use App\Filament\Imports\TestimonialImporter;
use App\Filament\Resources\Testimonials\TestimonialResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListTestimonials extends ListRecords
{
    protected static string $resource = TestimonialResource::class;

    public function getTitle(): string
    {
        return 'Testimoni';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah Testimoni'),
            ImportAction::make()
                ->importer(TestimonialImporter::class),
            ExportAction::make()
                ->exporter(TestimonialExporter::class),
        ];
    }
}
