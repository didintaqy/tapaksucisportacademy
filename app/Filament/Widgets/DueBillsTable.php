<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Payments\PaymentResource;
use App\Models\Payment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class DueBillsTable extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Tagihan Perlu Tindakan')
            ->query(fn (): Builder => Payment::outstanding()
                ->where(fn (Builder $query) => $query
                    ->whereNull('due_date')
                    ->orWhereDate('due_date', '<=', now()->addDays(7)))
                ->with('student')
                ->orderByRaw('due_date is null')
                ->orderBy('due_date'))
            ->columns([
                TextColumn::make('student.full_name')->label('Siswa')->searchable(),
                TextColumn::make('period')->label('Periode')->placeholder('-'),
                TextColumn::make('amount')->label('Nominal')->money('IDR'),
                TextColumn::make('status')->label('Status')->badge(),
                TextColumn::make('due_date')->label('Jatuh Tempo')->date('d M Y')->placeholder('Belum diatur'),
            ])
            ->recordUrl(fn (Payment $record): string => PaymentResource::getUrl('view', $record))
            ->paginated([8])
            ->defaultPaginationPageOption(8);
    }
}
