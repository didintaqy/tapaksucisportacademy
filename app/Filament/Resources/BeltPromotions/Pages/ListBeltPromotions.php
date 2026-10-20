<?php

namespace App\Filament\Resources\BeltPromotions\Pages;

use App\Filament\Resources\BeltPromotions\BeltPromotionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBeltPromotions extends ListRecords
{
    protected static string $resource = BeltPromotionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
