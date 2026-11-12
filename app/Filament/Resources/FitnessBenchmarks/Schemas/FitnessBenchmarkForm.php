<?php

namespace App\Filament\Resources\FitnessBenchmarks\Schemas;

use App\Models\FitnessTestItem;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FitnessBenchmarkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('fitness_test_item_id')
                    ->label('Item Tes Fisik')
                    ->options(fn () => FitnessTestItem::with('component')
                        ->orderBy('sort_order')
                        ->get()
                        ->mapWithKeys(fn (FitnessTestItem $item): array => [
                            $item->id => "{$item->name} ({$item->component?->name} • {$item->unit})",
                        ]))
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                        'Semua' => 'Semua Gender',
                    ])
                    ->default('L')
                    ->required(),

                TextInput::make('age_min')
                    ->label('Usia Minimum (Tahun)')
                    ->numeric()
                    ->minValue(3)
                    ->maxValue(80)
                    ->default(5)
                    ->required(),

                TextInput::make('age_max')
                    ->label('Usia Maksimum (Tahun)')
                    ->numeric()
                    ->minValue(3)
                    ->maxValue(80)
                    ->default(40)
                    ->required(),

                TextInput::make('target_value')
                    ->label('Target Benchmark')
                    ->numeric()
                    ->step('0.01')
                    ->required(),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }
}
