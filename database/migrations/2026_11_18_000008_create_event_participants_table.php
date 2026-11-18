<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fase B3 — Peserta event (lomba, UKT, pertemuan).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_participants', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->timestamp('registered_at')->nullable();
            $table->string('status', 20)->default('registered'); // registered|confirmed|attended|cancelled
            $table->decimal('fee_amount', 12, 2)->nullable();
            $table->string('result_note')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'student_id'], 'event_participants_event_student_unique');
            $table->index(['status', 'event_id'], 'event_participants_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_participants');
    }
};
