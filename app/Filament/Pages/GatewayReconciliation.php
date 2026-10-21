<?php

namespace App\Filament\Pages;

use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Models\Payment;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use UnitEnum;

class GatewayReconciliation extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static UnitEnum|string|null $navigationGroup = 'Keuangan';

    protected static ?string $navigationLabel = 'Rekonsiliasi Gateway';

    protected static ?string $title = 'Rekonsiliasi Pembayaran Gateway';

    protected static ?int $navigationSort = 7;

    protected string $view = 'filament.pages.gateway-reconciliation';

    #[Url]
    public ?string $gateway = null;

    #[Url]
    public bool $onlyMismatch = false;

    #[Url]
    public string $from = '';

    #[Url]
    public string $to = '';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(UserRole::Admin, UserRole::Operator) ?? false;
    }

    public function mount(): void
    {
        $this->from = $this->from !== '' ? $this->from : now()->subDays(30)->toDateString();
        $this->to = $this->to !== '' ? $this->to : now()->toDateString();
    }

    public function resetFilters(): void
    {
        $this->gateway = null;
        $this->onlyMismatch = false;
        $this->from = now()->subDays(30)->toDateString();
        $this->to = now()->toDateString();
    }

    public function reconcile(int $paymentId): void
    {
        $payment = Payment::find($paymentId);
        $expected = $payment?->expectedStatusFromGateway();

        if (! $payment || $expected === null) {
            Notification::make()
                ->title('Tidak ada data gateway untuk direkonsiliasi')
                ->warning()
                ->send();

            return;
        }

        if ($payment->status === $expected) {
            Notification::make()
                ->title('Status sudah selaras')
                ->info()
                ->send();

            return;
        }

        $payment->changeStatus(
            $expected,
            'Rekonsiliasi manual dari halaman admin',
            [
                'gateway' => $payment->gateway_name,
                'gateway_status' => $payment->gatewayTransactionStatus(),
            ],
        );

        Notification::make()
            ->title('Status '.$payment->invoice_number.' diselaraskan')
            ->body('Status baru: '.$expected->getLabel())
            ->success()
            ->send();
    }

    public function reconcileAll(): void
    {
        $count = 0;

        foreach ($this->filteredPayments() as $payment) {
            $expected = $payment->expectedStatusFromGateway();

            if ($expected === null || $payment->status === $expected) {
                continue;
            }

            $payment->changeStatus(
                $expected,
                'Rekonsiliasi massal dari halaman admin',
                [
                    'gateway' => $payment->gateway_name,
                    'gateway_status' => $payment->gatewayTransactionStatus(),
                ],
            );

            $count++;
        }

        Notification::make()
            ->title($count > 0 ? "{$count} pembayaran diselaraskan" : 'Tidak ada ketidaksesuaian')
            ->status($count > 0 ? 'success' : 'info')
            ->send();
    }

    protected function getViewData(): array
    {
        $payments = $this->filteredPayments();

        $mismatch = $payments->filter(fn (Payment $payment): bool => $payment->hasGatewayMismatch());

        return [
            'rows' => $payments,
            'gateways' => Payment::query()
                ->withGateway()
                ->whereNotNull('gateway_name')
                ->distinct()
                ->orderBy('gateway_name')
                ->pluck('gateway_name'),
            'summary' => [
                'total' => $payments->count(),
                'paid' => $payments->where('status', PaymentStatus::Paid)->count(),
                'pending' => $payments->where('status', PaymentStatus::Pending)->count(),
                'mismatch' => $mismatch->count(),
                'without_callback' => $payments->filter(fn (Payment $payment): bool => $payment->expectedStatusFromGateway() === null)->count(),
            ],
            'fromDate' => $this->from,
            'toDate' => $this->to,
        ];
    }

    /**
     * @return Collection<int, Payment>
     */
    private function filteredPayments(): Collection
    {
        return Payment::query()
            ->withGateway()
            ->with(['student', 'paymentType'])
            ->when($this->gateway, fn ($query) => $query->where('gateway_name', $this->gateway))
            ->when($this->from !== '', fn ($query) => $query->whereDate('updated_at', '>=', $this->from))
            ->when($this->to !== '', fn ($query) => $query->whereDate('updated_at', '<=', $this->to))
            ->latest('updated_at')
            ->get()
            ->when($this->onlyMismatch, fn (Collection $payments): Collection => $payments
                ->filter(fn (Payment $payment): bool => $payment->hasGatewayMismatch())
                ->values());
    }
}
