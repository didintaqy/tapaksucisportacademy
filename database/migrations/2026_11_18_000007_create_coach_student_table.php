<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fase B2 — Penugasan atlet ke pelatih.
 *
 * Pelatih saat ini hanya terhubung ke atlet lewat jadwal/cabang. Tabel pivot
 * ini memungkinkan scoping rapor, absensi, dan UKT per pelatih secara langsung.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coach_student', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('coach_id')->constrained('coaches')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->date('assigned_at');
            $table->date('released_at')->nullable();
            $table->timestamps();

            $table->unique(['coach_id', 'student_id'], 'coach_student_unique');
            $table->index(['student_id', 'released_at'], 'coach_student_student_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coach_student');
    }
};
