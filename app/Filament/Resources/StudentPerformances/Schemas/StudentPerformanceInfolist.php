<?php

namespace App\Filament\Resources\StudentPerformances\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StudentPerformanceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('student.id')
                    ->label('Student'),
                TextEntry::make('resting_heart_rate')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('max_heart_rate')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('training_heart_rate')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('height')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('weight')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('sit_and_reach')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('push_up_1_min')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('sit_up_2_min')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('core_stability_level')
                    ->placeholder('-'),
                TextEntry::make('triple_hop_right')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('triple_hop_left')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('illinois_agility')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('rast_average_power')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('rast_fatigue_index')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('beep_test_vo2max')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
