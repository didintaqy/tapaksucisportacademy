<?php

namespace App\Filament\Resources\Students\Schemas;

use App\Enums\BeltRank;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Siswa')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('student_code')
                            ->label('Kode Siswa')
                            ->copyable(),
                        TextEntry::make('full_name')
                            ->label('Nama Lengkap'),
                        TextEntry::make('nickname')
                            ->label('Nama Panggilan')
                            ->placeholder('-'),
                        TextEntry::make('gender')
                            ->label('Jenis Kelamin')
                            ->formatStateUsing(fn (?string $state): ?string => match ($state) {
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                                default => $state,
                            })
                            ->placeholder('-'),
                        TextEntry::make('birth_place')
                            ->label('Tempat Lahir')
                            ->placeholder('-'),
                        TextEntry::make('birth_date')
                            ->label('Tanggal Lahir')
                            ->date('d F Y')
                            ->placeholder('-'),
                        TextEntry::make('nik')
                            ->label('NIK')
                            ->placeholder('-'),
                        TextEntry::make('family_card_number')
                            ->label('No. Kartu Keluarga')
                            ->placeholder('-'),
                    ]),
                Section::make('Kontak & Keanggotaan')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('phone_number')
                            ->label('No. Telepon')
                            ->placeholder('-'),
                        TextEntry::make('email')
                            ->label('Email Wali/Siswa')
                            ->placeholder('-'),
                        TextEntry::make('branch')
                            ->label('Cabang')
                            ->badge()
                            ->placeholder('-'),
                        TextEntry::make('program.name')
                            ->label('Program Latihan')
                            ->badge()
                            ->placeholder('-'),
                        TextEntry::make('belt_level')
                            ->label('Tingkat Sabuk')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): ?string => BeltRank::tryFrom((string) $state)?->label() ?? $state)
                            ->color(fn (?string $state): string => BeltRank::tryFrom((string) $state)?->color() ?? 'gray')
                            ->placeholder('-'),
                        TextEntry::make('education')
                            ->label('Pendidikan')
                            ->placeholder('-'),
                        TextEntry::make('address')
                            ->label('Alamat')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),
                Section::make('Foto')
                    ->visible(fn ($record): bool => filled($record->photo))
                    ->schema([
                        ImageEntry::make('photo')
                            ->label('')
                            ->disk('public')
                            ->height(240),
                    ]),
                Section::make('Riwayat Data')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime('d F Y H:i'),
                        TextEntry::make('updated_at')
                            ->label('Diperbarui')
                            ->dateTime('d F Y H:i'),
                        TextEntry::make('deleted_at')
                            ->label('Dihapus')
                            ->dateTime('d F Y H:i')
                            ->visible(fn ($record): bool => $record->trashed()),
                    ]),
            ]);
    }
}
