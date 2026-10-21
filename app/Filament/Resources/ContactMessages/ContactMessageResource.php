<?php

namespace App\Filament\Resources\ContactMessages;

use App\Filament\Resources\ContactMessages\Pages\ListContactMessages;
use App\Filament\Resources\ContactMessages\Pages\ViewContactMessage;
use App\Filament\Resources\ContactMessages\Schemas\ContactMessageForm;
use App\Filament\Resources\ContactMessages\Schemas\ContactMessageInfolist;
use App\Filament\Resources\ContactMessages\Tables\ContactMessagesTable;
use App\Models\ContactMessage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ContactMessageResource extends Resource
{
    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Web';

    protected static ?string $navigationLabel = 'Kontak Masuk';

    protected static ?string $modelLabel = 'Pesan Kontak';

    protected static ?string $pluralModelLabel = 'Pesan Kontak Masuk';

    protected static ?int $navigationSort = 11;

    protected static ?string $model = ContactMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::EnvelopeOpen;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'unread')->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Pesan belum dibaca';
    }

    public static function form(Schema $schema): Schema
    {
        return ContactMessageForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ContactMessageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContactMessagesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactMessages::route('/'),
            'view' => ViewContactMessage::route('/{record}'),
        ];
    }
}
