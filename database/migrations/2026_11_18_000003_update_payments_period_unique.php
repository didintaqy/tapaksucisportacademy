<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fase A3 — Constraint pembayaran.
 *
 * Masalah: UNIQUE(student_id, period) melarang dua tagihan berbeda jenis pada
 * periode yang sama (mis. SPP + ujian + seragam di bulan yang sama).
 *
 * Solusi: sertakan payment_type_id. `period` tetap boleh NULL sehingga tagihan
 * sekali bayar (seragam, pendaftaran) tidak dibatasi — MySQL & SQLite
 * memperlakukan NULL sebagai nilai berbeda pada unique index.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table): void {
            $table->dropUnique('payments_student_period_unique');
        });

        Schema::table('payments', function (Blueprint $table): void {
            $table->unique(
                ['student_id', 'payment_type_id', 'period'],
                'payments_student_type_period_unique',
            );

            // Mempercepat laporan pemasukan per rentang tanggal.
            $table->index(['status', 'paid_at'], 'payments_status_paid_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table): void {
            $table->dropUnique('payments_student_type_period_unique');
            $table->dropIndex('payments_status_paid_at_index');
        });

        Schema::table('payments', function (Blueprint $table): void {
            // Catatan rollback: gagal bila sudah ada dua jenis tagihan pada
            // periode yang sama (perilaku baru). Bersihkan dulu bila perlu.
            $table->unique(['student_id', 'period'], 'payments_student_period_unique');
        });
    }
};
