<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Fase C4 — QR check-in absensi.
 *
 * Setiap siswa mendapat token QR unik. Absensi dapat menyimpan waktu check-in
 * dan metodenya (manual atau qr).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table): void {
            $table->string('qr_token', 64)->nullable()->unique()->after('student_code');
        });

        Schema::table('attendances', function (Blueprint $table): void {
            $table->timestamp('checked_in_at')->nullable()->after('notes');
            $table->string('checkin_method', 20)->nullable()->after('checked_in_at'); // manual | qr
        });

        $this->backfillQrTokens();
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table): void {
            $table->dropColumn(['checked_in_at', 'checkin_method']);
        });

        Schema::table('students', function (Blueprint $table): void {
            $table->dropUnique(['qr_token']);
            $table->dropColumn('qr_token');
        });
    }

    private function backfillQrTokens(): void
    {
        DB::table('students')
            ->whereNull('qr_token')
            ->orderBy('id')
            ->chunkById(200, function ($students): void {
                foreach ($students as $student) {
                    do {
                        $token = Str::random(40);
                        $exists = DB::table('students')->where('qr_token', $token)->exists();
                    } while ($exists);

                    DB::table('students')->where('id', $student->id)->update(['qr_token' => $token]);
                }
            });
    }
};
