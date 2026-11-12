<?php

namespace App\Filament\Imports;

use Filament\Actions\Imports\Importer;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Model;

abstract class BaseImporter extends Importer
{
    /**
     * Kolom unik yang dipakai untuk mendeteksi duplikat saat impor.
     * Kembalikan null bila importer hanya membuat record baru (tanpa deteksi duplikat).
     *
     * @return array<int, string>|null
     */
    protected static function uniqueColumns(): ?array
    {
        return null;
    }

    public static function getOptionsFormComponents(): array
    {
        return [
            Toggle::make('replace_existing')
                ->label('Timpa data yang sudah ada')
                ->helperText('Jika aktif, baris duplikat akan memperbarui record yang sudah ada. Jika nonaktif, baris duplikat dilewati (data lama tetap utuh).')
                ->default(false),
        ];
    }

    public function resolveRecord(): ?Model
    {
        $modelClass = static::getModel();
        $uniqueColumns = static::uniqueColumns();

        if (blank($uniqueColumns)) {
            return new $modelClass;
        }

        $query = $modelClass::query();

        foreach ($uniqueColumns as $column) {
            if (! array_key_exists($column, $this->data) || blank($this->data[$column])) {
                return new $modelClass;
            }

            $query->where($column, $this->data[$column]);
        }

        $existing = $query->first();

        if (! $existing) {
            return new $modelClass;
        }

        if (! (bool) ($this->options['replace_existing'] ?? false)) {
            // Lewati baris duplikat: data lama tetap utuh (aturan: jangan merubah data yang sudah benar).
            return null;
        }

        return $existing;
    }
}
