<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Fase A2 — Unifikasi identitas pelatih (aditif).
 *
 * Sumber masalah: `training_schedules.coach_id` menunjuk ke `users`, sedangkan
 * profil pelatih publik ada di `coaches`. Migrasi ini menautkan keduanya tanpa
 * memutus kode lama:
 *
 *  - `coaches.user_id`  : akun login pelatih (opsional untuk pelatih non-akun).
 *  - `training_schedules.coach_ref_id` : referensi ke `coaches`.
 *
 * `coach_id` lama tetap ada sampai aplikasi selesai dipindahkan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coaches', function (Blueprint $table): void {
            $table->foreignId('user_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete();

            $table->unique('user_id');
        });

        Schema::table('training_schedules', function (Blueprint $table): void {
            $table->foreignId('coach_ref_id')
                ->nullable()
                ->after('coach_id')
                ->constrained('coaches')
                ->nullOnDelete();
        });

        $this->linkCoachesToUsers();
        $this->backfillScheduleCoachRefs();
    }

    public function down(): void
    {
        Schema::table('training_schedules', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('coach_ref_id');
        });

        Schema::table('coaches', function (Blueprint $table): void {
            $table->dropUnique(['user_id']);
            $table->dropConstrainedForeignId('user_id');
        });
    }

    /**
     * Cocokkan profil pelatih ke akun ber-role `pelatih` berdasarkan nama.
     * Hanya pencocokan unik yang diambil; nama ganda dibiarkan untuk ditinjau admin.
     */
    private function linkCoachesToUsers(): void
    {
        $users = DB::table('users')
            ->where('role', 'pelatih')
            ->get(['id', 'name']);

        $byName = [];

        foreach ($users as $user) {
            $key = mb_strtolower(trim((string) $user->name));
            $byName[$key][] = $user->id;
        }

        foreach (DB::table('coaches')->whereNull('user_id')->get(['id', 'name']) as $coach) {
            $key = mb_strtolower(trim((string) $coach->name));
            $matches = $byName[$key] ?? [];

            if (count($matches) !== 1) {
                continue;
            }

            DB::table('coaches')->where('id', $coach->id)->update(['user_id' => $matches[0]]);
        }
    }

    private function backfillScheduleCoachRefs(): void
    {
        $coachByUser = DB::table('coaches')
            ->whereNotNull('user_id')
            ->pluck('id', 'user_id');

        if ($coachByUser->isEmpty()) {
            return;
        }

        DB::table('training_schedules')
            ->whereNotNull('coach_id')
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($coachByUser): void {
                foreach ($rows as $row) {
                    $coachId = $coachByUser[$row->coach_id] ?? null;

                    if ($coachId === null) {
                        continue;
                    }

                    DB::table('training_schedules')
                        ->where('id', $row->id)
                        ->update(['coach_ref_id' => $coachId]);
                }
            });
    }
};
