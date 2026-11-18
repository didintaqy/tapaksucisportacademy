<?php

namespace App\Filament\Resources\EventParticipants;

use App\Filament\Resources\EventParticipants\Pages\CreateEventParticipant;
use App\Filament\Resources\EventParticipants\Pages\EditEventParticipant;
use App\Filament\Resources\EventParticipants\Pages\ListEventParticipants;
use App\Filament\Resources\EventParticipants\Schemas\EventParticipantForm;
use App\Filament\Resources\EventParticipants\Tables\EventParticipantsTable;
use App\Models\EventParticipant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class EventParticipantResource extends Resource
{
    protected static ?string $model = EventParticipant::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static UnitEnum|string|null $navigationGroup = 'Manajemen Web';

    protected static ?int $navigationSort = 14;

    public static function getNavigationLabel(): string
    {
        return 'Peserta Event';
    }

    public static function getModelLabel(): string
    {
        return 'Peserta Event';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Peserta Event';
    }

    public static function form(Schema $schema): Schema
    {
        return EventParticipantForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventParticipantsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEventParticipants::route('/'),
            'create' => CreateEventParticipant::route('/create'),
            'edit' => EditEventParticipant::route('/{record}/edit'),
        ];
    }
}
