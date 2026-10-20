<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Unique index untuk mencegah double tagihan per siswa per periode (utama iuran bulanan)
        Schema::table('payments', function (Blueprint $table): void {
            $table->unique(['student_id', 'period'], 'payments_student_period_unique');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table): void {
            $table->dropUnique('payments_student_period_unique');
        });
    }
};
