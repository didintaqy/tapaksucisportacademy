<?php

namespace App\Services\Payroll;

use App\Enums\PayrollStatus;
use App\Models\Coach;
use App\Models\CoachPayroll;
use App\Models\CoachRate;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Menghitung honor pelatih dari data absensi dan tarif yang berlaku.
 *
 * Sesi dihitung dari kombinasi unik (jadwal, tanggal) pada absensi untuk
 * jadwal yang ditugaskan ke pelatih tersebut.
 */
class CoachPayrollCalculator
{
    public function sessionsHeld(Coach $coach, string $period): int
    {
        [$year, $month] = $this->splitPeriod($period);

        return DB::table('attendances')
            ->join('training_schedules', 'training_schedules.id', '=', 'attendances.training_schedule_id')
            ->where('training_schedules.coach_ref_id', $coach->id)
            ->whereYear('attendances.date', $year)
            ->whereMonth('attendances.date', $month)
            ->distinct()
            ->get(['attendances.training_schedule_id', 'attendances.date'])
            ->count();
    }

    public function rateFor(Coach $coach, string $period): ?CoachRate
    {
        [$year, $month] = $this->splitPeriod($period);

        $start = sprintf('%04d-%02d-01', $year, $month);
        $end = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

        return CoachRate::query()
            ->where('coach_id', $coach->id)
            ->active()
            ->whereDate('effective_from', '<=', $end)
            ->where(function ($query) use ($start): void {
                $query->whereNull('effective_to')
                    ->orWhereDate('effective_to', '>=', $start);
            })
            ->latest('effective_from')
            ->first();
    }

    /**
     * Membuat atau memperbarui draf payroll. Payroll yang sudah disetujui
     * atau dibayar tidak ditimpa.
     */
    public function draftFor(Coach $coach, string $period, ?int $sessions = null): CoachPayroll
    {
        $payroll = CoachPayroll::firstOrNew([
            'coach_id' => $coach->id,
            'period' => $period,
        ]);

        if ($payroll->exists && $payroll->status !== PayrollStatus::Draft) {
            return $payroll;
        }

        $rate = $this->rateFor($coach, $period);

        $payroll->fill([
            'sessions_count' => $sessions ?? $this->sessionsHeld($coach, $period),
            'rate_type' => $rate?->rate_type ?? 'per_session',
            'rate_amount' => $rate?->amount ?? 0,
            'status' => PayrollStatus::Draft,
        ]);

        $payroll->save();

        return $payroll;
    }

    /**
     * @return array{0: int, 1: int}
     */
    private function splitPeriod(string $period): array
    {
        $parts = explode('-', $period);

        return [
            (int) ($parts[0] ?? now()->year),
            (int) ($parts[1] ?? now()->month),
        ];
    }
}
