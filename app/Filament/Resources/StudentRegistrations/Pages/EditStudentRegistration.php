<?php

namespace App\Filament\Resources\StudentRegistrations\Pages;

use App\Enums\RegistrationStatus;
use App\Filament\Resources\StudentRegistrations\StudentRegistrationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStudentRegistration extends EditRecord
{
    protected static string $resource = StudentRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['status'] !== RegistrationStatus::New->value && $this->record->reviewed_at === null) {
            $data['reviewed_by'] = auth()->id();
            $data['reviewed_at'] = now();
        }

        return $data;
    }
}
