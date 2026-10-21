<?php

namespace App\Filament\Pages;

use App\Enums\PaymentMethod;
use App\Enums\UserRole;
use App\Models\PaymentType;
use App\Services\Reports\FinancialReportService;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Url;
use UnitEnum;

class IncomeReport extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static UnitEnum|string|null $navigationGroup = 'Keuangan';

    protected static ?string $navigationLabel = 'Rekap Pemasukan';

    protected static ?string $title = 'Rekap Pemasukan';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.income-report';

    #[Url]
    public string $from = '';

    #[Url]
    public string $to = '';

    #[Url]
    public ?string $type = null;

    #[Url]
    public ?string $method = null;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(UserRole::Admin, UserRole::Operator) ?? false;
    }

    public function mount(): void
    {
        $this->from = $this->from !== '' ? $this->from : now()->startOfMonth()->toDateString();
        $this->to = $this->to !== '' ? $this->to : now()->toDateString();
    }

    public function resetFilters(): void
    {
        $this->from = now()->startOfMonth()->toDateString();
        $this->to = now()->toDateString();
        $this->type = null;
        $this->method = null;
    }

    protected function getViewData(): array
    {
        $service = app(FinancialReportService::class);

        $from = Carbon::parse($this->from ?: now()->startOfMonth()->toDateString())->startOfDay();
        $to = Carbon::parse($this->to ?: now()->toDateString())->endOfDay();

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        $typeId = filled($this->type) ? (int) $this->type : null;
        $method = filled($this->method) ? PaymentMethod::tryFrom((string) $this->method) : null;

        return [
            'summary' => $service->incomeSummary($from, $to, $typeId, $method),
            'byType' => $service->incomeByType($from, $to, $typeId, $method),
            'byMonth' => $service->incomeByMonth($from, $to, $typeId, $method),
            'types' => PaymentType::orderBy('name')->pluck('name', 'id'),
            'methods' => PaymentMethod::cases(),
            'fromDate' => $from,
            'toDate' => $to,
        ];
    }
}
