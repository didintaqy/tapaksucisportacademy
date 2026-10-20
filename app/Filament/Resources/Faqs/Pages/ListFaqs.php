<?php

namespace App\Filament\Resources\Faqs\Pages;

use App\Filament\Resources\Faqs\FaqResource;
use Filament\Actions\CreateAction;
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
        ];
    }
}
