<?php

namespace App\Services\Reports;

use App\Enums\PaymentMethod;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class FinancialReportService
{
    /**
     * Query dasar pemasukan (pembayaran lunas) pada rentang tanggal.
     */
    public function incomeQuery(
        Carbon $from,
        Carbon $to,
        ?int $typeId = null,
        ?PaymentMethod $method = null,
    ): Builder {
        return Payment::query()
            ->paid()
            ->whereBetween('paid_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->when($typeId, fn (Builder $query): Builder => $query->where('payment_type_id', $typeId))
            ->when($method, fn (Builder $query): Builder => $query->where('payment_method', $method->value));
    }

    /**
     * @return array{total: float, count: int, average: float}
     */
    public function incomeSummary(
        Carbon $from,
        Carbon $to,
        ?int $typeId = null,
        ?PaymentMethod $method = null,
    ): array {
        $query = $this->incomeQuery($from, $to, $typeId, $method);

        $total = (float) (clone $query)->sum('amount');
        $count = (clone $query)->count();

        return [
            'total' => $total,
            'count' => $count,
            'average' => $count > 0 ? $total / $count : 0.0,
        ];
    }

    /**
     * Rekap pemasukan per jenis pembayaran.
     *
     * @return Collection<int, Payment>
     */
    public function incomeByType(
        Carbon $from,
        Carbon $to,
        ?int $typeId = null,
        ?PaymentMethod $method = null,
    ): Collection {
        return $this->incomeQuery($from, $to, $typeId, $method)
            ->selectRaw('payment_type_id, COUNT(*) as transactions, SUM(amount) as total')
            ->groupBy('payment_type_id')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Rekap pemasukan per bulan dalam rentang.
     *
     * @return array<int, array{period: string, label: string, total: float, transactions: int}>
     */
    public function incomeByMonth(
        Carbon $from,
        Carbon $to,
        ?int $typeId = null,
        ?PaymentMethod $method = null,
    ): array {
        $rows = [];

        $cursor = $from->copy()->startOfMonth();
        $end = $to->copy()->endOfMonth();

        while ($cursor->lessThanOrEqualTo($end)) {
            $monthStart = $cursor->copy()->startOfMonth();
            $monthEnd = $cursor->copy()->endOfMonth();

            $query = $this->incomeQuery($monthStart, $monthEnd, $typeId, $method);

            $rows[] = [
                'period' => $cursor->format('Y-m'),
                'label' => $cursor->copy()->locale('id')->translatedFormat('F Y'),
                'total' => (float) (clone $query)->sum('amount'),
                'transactions' => (clone $query)->count(),
            ];

            $cursor->addMonth();
        }

        return $rows;
    }

    /**
     * Query tunggakan (belum lunas / menunggu pembayaran).
     */
    public function outstandingQuery(?int $typeId = null, ?string $search = null): Builder
    {
        return Payment::query()
            ->outstanding()
            ->when($typeId, fn (Builder $query): Builder => $query->where('payment_type_id', $typeId))
            ->when($search, function (Builder $query) use ($search): void {
                $query->whereHas('student', function (Builder $studentQuery) use ($search): void {
                    $studentQuery
                        ->where('full_name', 'like', "%{$search}%")
                        ->orWhere('student_code', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%");
                });
            });
    }

    /**
     * Rekap tunggakan per siswa.
     *
     * @return Collection<int, Payment>
     */
    public function outstandingByStudent(?int $typeId = null, ?string $search = null): Collection
    {
        return $this->outstandingQuery($typeId, $search)
            ->selectRaw('student_id, COUNT(*) as bills, SUM(amount) as total, MIN(due_date) as oldest_due_date')
            ->groupBy('student_id')
            ->orderByDesc('total')
            ->with('student:id,student_code,full_name,branch,phone_number')
            ->get();
    }

    /**
     * @return array{total: float, count: int, students: int, overdue: int}
     */
    public function outstandingSummary(?int $typeId = null, ?string $search = null): array
    {
        $query = $this->outstandingQuery($typeId, $search);

        return [
            'total' => (float) (clone $query)->sum('amount'),
            'count' => (clone $query)->count(),
            'students' => (int) (clone $query)->distinct()->count('student_id'),
            'overdue' => (clone $query)
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', now()->toDateString())
                ->count(),
        ];
    }
}
