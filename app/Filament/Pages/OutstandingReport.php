<?php

namespace App\Filament\Pages;

use App\Enums\UserRole;
use App\Models\PaymentType;
use App\Services\Reports\FinancialReportService;
use BackedEnum;
use Filament\Pages\Page;
use Livewire\Attributes\Url;
use UnitEnum;

class OutstandingReport extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static UnitEnum|string|null $navigationGroup = 'Keuangan';

    protected static ?string $navigationLabel = 'Laporan Tunggakan';

    protected static ?string $title = 'Laporan Tunggakan per Siswa';

    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.pages.outstanding-report';

    #[Url]
    public ?string $type = null;

    #[Url]
    public string $search = '';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(UserRole::Admin, UserRole::Operator) ?? false;
    }

    public function resetFilters(): void
    {
        $this->type = null;
        $this->search = '';
    }

    protected function getViewData(): array
    {
        $service = app(FinancialReportService::class);

        $typeId = filled($this->type) ? (int) $this->type : null;
        $search = filled($this->search) ? trim($this->search) : null;

        return [
            'summary' => $service->outstandingSummary($typeId, $search),
            'rows' => $service->outstandingByStudent($typeId, $search),
            'types' => PaymentType::orderBy('name')->pluck('name', 'id'),
        ];
    }
}
