<?php

namespace App\Filament\Resources\StudentPerformances\Schemas;

use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Schema;

class StudentPerformanceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                ViewEntry::make('performance_report')
                    ->hiddenLabel()
                    ->view('filament.resources.student-performances.components.visual-data')
                    ->columnSpanFull(),
            ]);
    }
}
