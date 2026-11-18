<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fase C2 — Honor / payroll pelatih.
 *
 * `coach_rates` menyimpan tarif per pelatih (per sesi atau per bulan) beserta
 * masa berlaku. `coach_payrolls` menyimpan rekap per periode (YYYY-MM).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coach_rates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('coach_id')->constrained('coaches')->cascadeOnDelete();
            $table->string('rate_type', 20)->default('per_session'); // per_session | per_month
            $table->decimal('amount', 12, 2);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['coach_id', 'is_active', 'effective_from'], 'coach_rates_lookup_index');
        });

        Schema::create('coach_payrolls', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('coach_id')->constrained('coaches')->cascadeOnDelete();
            $table->string('period', 7); // YYYY-MM
            $table->unsignedInteger('sessions_count')->default(0);
            $table->string('rate_type', 20)->default('per_session'); // per_session | per_month
            $table->decimal('rate_amount', 12, 2)->default(0);
            $table->decimal('bonus', 12, 2)->default(0);
            $table->decimal('deduction', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('status', 20)->default('draft'); // draft | approved | paid
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['coach_id', 'period'], 'coach_payrolls_coach_period_unique');
            $table->index(['period', 'status'], 'coach_payrolls_period_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coach_payrolls');
        Schema::dropIfExists('coach_rates');
    }
};
