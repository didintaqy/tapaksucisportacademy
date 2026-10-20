<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Filament\Resources\Students\Widgets\StudentOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageStudents extends ManageRecords
{
    protected static string $resource = StudentResource::class;

    protected static ?string $title = 'Data Siswa TSSA';

    protected ?string $subheading = 'Kelola data identitas, jenjang pendidikan, dan riwayat tingkatan sabuk siswa.';

    protected function getHeaderWidgets(): array
    {
        return [
            StudentOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
