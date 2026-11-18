<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fase A5 — Audit log umum.
 *
 * `payment_status_histories` hanya mencatat status pembayaran. Tabel ini
 * mencatat perubahan pada entitas sensitif lain (siswa, UKT, absensi,
 * pengaturan situs): siapa, kapan, dan nilai lama/baru.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_log', function (Blueprint $table): void {
            $table->id();
            $table->string('subject_type', 120);
            $table->unsignedBigInteger('subject_id');
            $table->foreignId('causer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event', 20);          // created | updated | deleted | restored
            $table->json('properties')->nullable(); // { old: {...}, attributes: {...} }
            $table->timestamps();

            $table->index(['subject_type', 'subject_id', 'created_at'], 'activity_log_subject_index');
            $table->index(['causer_id', 'created_at'], 'activity_log_causer_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_log');
    }
};
