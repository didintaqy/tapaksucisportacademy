<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Exports\StudentExporter;
use App\Filament\Imports\StudentImporter;
use App\Filament\Imports\StudentImportTemplate;
use App\Filament\Resources\Students\StudentResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('downloadImportTemplate')
                ->label('Template Impor')
                ->icon(Heroicon::OutlinedDocumentArrowDown)
                ->color('gray')
                ->tooltip('Unduh template CSV untuk impor data siswa')
                ->action(fn () => response()->streamDownload(
                    function (): void {
                        echo StudentImportTemplate::contents();
                    },
                    StudentImportTemplate::fileName(),
                    ['Content-Type' => 'text/csv; charset=UTF-8'],
                )),
            ImportAction::make()
                ->label('Impor Siswa')
                ->modalHeading('Impor Data Siswa')
                ->importer(StudentImporter::class),
            ExportAction::make()
                ->exporter(StudentExporter::class),
        ];
    }
}
