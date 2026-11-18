<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fase C1 — Wali / orang tua.
 *
 * Satu wali dapat terhubung ke beberapa siswa (mis. orang tua dengan dua anak),
 * dan satu siswa dapat punya beberapa wali (ayah, ibu, wali).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guardians', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('phone', 40)->nullable();
            $table->string('email')->nullable();
            $table->string('occupation')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        Schema::create('guardian_student', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('guardian_id')->constrained('guardians')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('relation', 20)->default('wali'); // ayah | ibu | wali
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->unique(['guardian_id', 'student_id'], 'guardian_student_unique');
            $table->index(['student_id', 'is_primary'], 'guardian_student_student_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guardian_student');
        Schema::dropIfExists('guardians');
    }
};
