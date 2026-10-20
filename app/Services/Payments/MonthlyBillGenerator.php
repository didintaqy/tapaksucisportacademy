<?php

namespace App\Services\Payments;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Student;
use Illuminate\Support\Carbon;

class MonthlyBillGenerator
{
    /**
     * @return array{created: int, skipped: int}
     */
    public function generate(PaymentType $type, Carbon $month, ?int $createdBy = null): array
    {
        $period = $month->format('Y-m');
        $dueDate = $month->copy()->endOfMonth()->toDateString();
        $description = $type->name.' '.$month->copy()->locale('id')->translatedFormat('F Y');

        $existing = Payment::query()
            ->where('payment_type_id', $type->id)
            ->where('period', $period)
            ->pluck('student_id');

        $created = 0;
        $skipped = $existing->count();

        Student::query()
            ->whereNotIn('id', $existing)
            ->orderBy('id')
            ->each(function (Student $student) use ($type, $period, $dueDate, $description, $createdBy, &$created): void {
                Payment::create([
                    'student_id' => $student->id,
                    'payment_type_id' => $type->id,
                    'period' => $period,
                    'description' => $description,
                    'amount' => $type->default_amount,
                    'status' => PaymentStatus::Unpaid,
                    'due_date' => $dueDate,
                    'created_by' => $createdBy,
                ]);

                $created++;
            });

        return ['created' => $created, 'skipped' => $skipped];
    }
}
