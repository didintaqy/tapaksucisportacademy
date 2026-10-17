<?php

namespace App\Filament\Resources\Galleries;

use App\Filament\Resources\Galleries\Pages\CreateGallery;
use App\Filament\Resources\Galleries\Pages\EditGallery;
use App\Filament\Resources\Galleries\Pages\ListGalleries;
use App\Filament\Resources\Galleries\Schemas\GalleryForm;
use App\Filament\Resources\Galleries\Tables\GalleriesTable;
use App\Models\Gallery;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class GalleryResource extends Resource
{
    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Web';

    protected static ?string $navigationLabel = 'Galeri Kegiatan';

    protected static ?string $modelLabel = 'Foto Galeri';

    protected static ?string $pluralModelLabel = 'Galeri Kegiatan';

    protected static ?int $navigationSort = 1;

    protected static ?string $model = Gallery::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Photo;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('is_published', true)->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Jumlah foto yang tampil di website';
    }

    public static function form(Schema $schema): Schema
    {
        return GalleryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GalleriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGalleries::route('/'),
            'create' => CreateGallery::route('/create'),
            'edit' => EditGallery::route('/{record}/edit'),
        ];
    }
}
