<?php

namespace App\Filament\Resources\StudentPerformances\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema; 

class StudentPerformanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_id')
                    ->relationship('student', 'nickname')
                    ->required(),
                TextInput::make('resting_heart_rate')
                    ->numeric(),
                TextInput::make('max_heart_rate')
                    ->numeric(),
                TextInput::make('training_heart_rate')
                    ->numeric(),
                TextInput::make('height')
                    ->numeric(),
                TextInput::make('weight')
                    ->numeric(),
                TextInput::make('sit_and_reach')
                    ->numeric(),
                TextInput::make('push_up_1_min')
                    ->numeric(),
                TextInput::make('sit_up_2_min')
                    ->numeric(),
                Select::make('core_stability_level')
                    ->options([
                        '1' => 'Level 1',
                        '2' => 'Level 2',
                        '3' => 'Level 3',
                        '4' => 'Level 4',
                        '5' => 'Level 5',
                        '6' => 'Level 6',
                        '7' => 'Level 7',
                        '8' => 'Level 8',
                        '9' => 'Level 9',
                        '10' => 'Level 10',
                        '11' => 'Level 11',
                        '12' => 'Level 12',
                    ]),
                TextInput::make('triple_hop_right')
                    ->numeric(),
                TextInput::make('triple_hop_left')
                    ->numeric(),
                TextInput::make('illinois_agility')
                    ->numeric(),
                TextInput::make('rast_average_power')
                    ->numeric(),
                TextInput::make('rast_fatigue_index')
                    ->numeric(),
                TextInput::make('beep_test_vo2max')
                    ->numeric(),
            ]);
    }
}
