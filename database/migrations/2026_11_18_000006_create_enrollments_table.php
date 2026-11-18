<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Fase B1 — Keanggotaan / riwayat program.
 *
 * `students.program_id` hanya menyimpan program aktif. Tabel ini menyimpan
 * riwayat: kapan mulai, kapan selesai, dan status (aktif/cuti/berhenti/lulus).
 *
 * `active_key` (diisi model) + unique index menegakkan aturan "satu
 * keanggotaan aktif per siswa". Nilai NULL untuk baris non-aktif, sehingga
 * banyak baris lama boleh ada.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('program_id')->constrained('programs')->restrictOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('coach_id')->nullable()->constrained('coaches')->nullOnDelete();
            $table->string('status', 20)->default('active');
            $table->date('started_at');
            $table->date('ended_at')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('active_key')->nullable()->unique();
            $table->timestamps();

            $table->index(['student_id', 'status'], 'enrollments_student_status_index');
            $table->index(['program_id', 'branch_id'], 'enrollments_program_branch_index');
        });

        $this->backfillFromStudents();
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }

    private function backfillFromStudents(): void
    {
        $now = now();

        DB::table('students')
            ->whereNotNull('program_id')
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->chunkById(200, function ($students) use ($now): void {
                foreach ($students as $student) {
                    $startedAt = $student->created_at
                        ? substr((string) $student->created_at, 0, 10)
                        : $now->toDateString();

                    DB::table('enrollments')->insert([
                        'student_id' => $student->id,
                        'program_id' => $student->program_id,
                        'branch_id' => $student->branch_id ?? null,
                        'coach_id' => null,
                        'status' => 'active',
                        'started_at' => $startedAt,
                        'ended_at' => null,
                        'notes' => null,
                        'active_key' => $student->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            });
    }
};
