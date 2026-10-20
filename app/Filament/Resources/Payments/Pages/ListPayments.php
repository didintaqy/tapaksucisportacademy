<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Enums\PaymentPeriodType;
use App\Filament\Resources\Payments\PaymentResource;
use App\Filament\Resources\PaymentTypes\PaymentTypeResource;
use App\Models\PaymentType;
use App\Services\Payments\MonthlyBillGenerator;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Pembayaran Siswa';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('payment_types')
                ->label('Jenis Pembayaran')
                ->icon('heroicon-o-banknotes')
                ->color('gray')
                ->url(PaymentTypeResource::getUrl('index')),
            Action::make('generate_monthly')
                ->label('Generate Tagihan Bulanan')
                ->icon('heroicon-o-document-duplicate')
                ->schema([
                    Select::make('payment_type_id')
                        ->label('Jenis Iuran')
                        ->options(fn () => PaymentType::query()
                            ->active()
                            ->where('period_type', PaymentPeriodType::Monthly)
                            ->orderBy('sort_order')
                            ->pluck('name', 'id'))
                        ->required()
                        ->native(false),
                    Select::make('period')
                        ->label('Periode')
                        ->options(fn () => collect(range(0, 2))
                            ->mapWithKeys(function (int $offset): array {
                                $month = now()->addMonths($offset)->locale('id');

                                return [$month->format('Y-m') => $month->translatedFormat('F Y')];
                            }))
                        ->default(now()->format('Y-m'))
                        ->required()
                        ->native(false),
                ])
                ->action(function (array $data, MonthlyBillGenerator $generator): void {
                    $type = PaymentType::query()->findOrFail($data['payment_type_id']);
                    $result = $generator->generate(
                        $type,
                        Carbon::createFromFormat('Y-m', $data['period'])->startOfMonth(),
                        auth()->id(),
                    );

                    Notification::make()
                        ->title('Tagihan bulanan dibuat')
                        ->body("{$result['created']} tagihan baru, {$result['skipped']} siswa dilewati karena sudah punya tagihan periode ini.")
                        ->success()
                        ->send();
                }),
            CreateAction::make()
                ->label('Tambah Pembayaran'),
        ];
    }
}
