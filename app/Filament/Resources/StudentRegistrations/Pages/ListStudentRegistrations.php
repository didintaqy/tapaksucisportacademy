<?php

namespace App\Filament\Resources\StudentRegistrations\Pages;

use App\Filament\Resources\StudentRegistrations\StudentRegistrationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStudentRegistrations extends ListRecords
{
    protected static string $resource = StudentRegistrationResource::class;

    public function getTitle(): string
    {
        return 'Pendaftaran Siswa';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah Pendaftaran'),
        ];
    }
}
