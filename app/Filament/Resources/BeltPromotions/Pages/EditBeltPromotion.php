<?php

namespace App\Filament\Resources\BeltPromotions\Pages;

use App\Filament\Resources\BeltPromotions\BeltPromotionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBeltPromotion extends EditRecord
{
    protected static string $resource = BeltPromotionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
