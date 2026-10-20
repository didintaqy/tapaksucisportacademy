<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardWidgetsTest extends TestCase
{
    use RefreshDatabase;

    public function test_due_bills_query_includes_only_outstanding_due_within_seven_days(): void
    {
        $student = Student::factory()->create();

        $due = Payment::factory()->create([
            'student_id' => $student->id,
            'status' => PaymentStatus::Unpaid,
            'due_date' => now()->addDays(3),
        ]);

        Payment::factory()->paid()->create([
            'student_id' => $student->id,
            'due_date' => now()->addDays(2),
        ]);

        Payment::factory()->create([
            'student_id' => $student->id,
            'status' => PaymentStatus::Unpaid,
            'due_date' => now()->addDays(20),
        ]);

        $ids = Payment::outstanding()
            ->where(fn ($query) => $query
                ->whereNull('due_date')
                ->orWhereDate('due_date', '<=', now()->addDays(7)))
            ->pluck('id');

        $this->assertTrue($ids->contains($due->id));
        $this->assertCount(1, $ids);
    }
}
