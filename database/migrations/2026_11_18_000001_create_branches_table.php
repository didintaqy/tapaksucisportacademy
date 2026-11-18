<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Fase A1 — Tabel cabang.
 *
 * Menambahkan `branches` sebagai sumber data cabang yang ternormalisasi,
 * lalu mem-backfill `branch_id` dari kolom string yang sudah ada.
 *
 * Kolom string lama (students.branch, training_schedules.branch,
 * student_registrations.branch, programs.branches) TIDAK dihapus pada
 * migrasi ini. Penghapusan dilakukan setelah aplikasi membaca branch_id.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->string('slug', 120)->unique();
            $table->text('address')->nullable();
            $table->string('phone', 40)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $names = $this->collectBranchNames();

        foreach ($names as $name) {
            $slug = Str::slug($name);

            if ($slug === '') {
                continue;
            }

            DB::table('branches')->updateOrInsert(
                ['slug' => $slug],
                ['name' => $name, 'is_active' => true, 'sort_order' => 0, 'created_at' => now(), 'updated_at' => now()],
            );
        }

        Schema::table('students', function (Blueprint $table): void {
            $table->foreignId('branch_id')->nullable()->after('branch')->constrained('branches')->nullOnDelete();
        });

        Schema::table('training_schedules', function (Blueprint $table): void {
            $table->foreignId('branch_id')->nullable()->after('branch')->constrained('branches')->nullOnDelete();
        });

        Schema::table('student_registrations', function (Blueprint $table): void {
            $table->foreignId('branch_id')->nullable()->after('branch')->constrained('branches')->nullOnDelete();
        });

        $this->backfillBranchIds('students');
        $this->backfillBranchIds('training_schedules');
        $this->backfillBranchIds('student_registrations');

        Schema::create('program_branch', function (Blueprint $table): void {
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->primary(['program_id', 'branch_id']);
        });

        $this->backfillProgramBranches();
    }

    public function down(): void
    {
        Schema::dropIfExists('program_branch');

        Schema::table('student_registrations', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('branch_id');
        });

        Schema::table('training_schedules', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('branch_id');
        });

        Schema::table('students', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('branch_id');
        });

        Schema::dropIfExists('branches');
    }

    /**
     * @return array<int, string>
     */
    private function collectBranchNames(): array
    {
        $names = [];

        foreach (['students', 'training_schedules', 'student_registrations'] as $table) {
            $values = DB::table($table)
                ->whereNotNull('branch')
                ->where('branch', '<>', '')
                ->distinct()
                ->pluck('branch');

            foreach ($values as $value) {
                $names[] = trim((string) $value);
            }
        }

        foreach (DB::table('programs')->whereNotNull('branches')->pluck('branches') as $json) {
            $decoded = json_decode((string) $json, true);

            if (! is_array($decoded)) {
                continue;
            }

            foreach ($decoded as $value) {
                if (is_string($value) && trim($value) !== '') {
                    $names[] = trim($value);
                }
            }
        }

        return array_values(array_unique(array_filter($names)));
    }

    private function backfillBranchIds(string $table): void
    {
        $branches = DB::table('branches')->pluck('id', 'slug');

        DB::table($table)
            ->whereNotNull('branch')
            ->where('branch', '<>', '')
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($table, $branches): void {
                foreach ($rows as $row) {
                    $slug = Str::slug(trim((string) $row->branch));

                    if (! isset($branches[$slug])) {
                        continue;
                    }

                    DB::table($table)
                        ->where('id', $row->id)
                        ->update(['branch_id' => $branches[$slug]]);
                }
            });
    }

    private function backfillProgramBranches(): void
    {
        $branches = DB::table('branches')->pluck('id', 'slug');

        foreach (DB::table('programs')->whereNotNull('branches')->get(['id', 'branches']) as $program) {
            $decoded = json_decode((string) $program->branches, true);

            if (! is_array($decoded)) {
                continue;
            }

            foreach ($decoded as $value) {
                if (! is_string($value) || trim($value) === '') {
                    continue;
                }

                $slug = Str::slug(trim($value));

                if (! isset($branches[$slug])) {
                    continue;
                }

                DB::table('program_branch')->updateOrInsert([
                    'program_id' => $program->id,
                    'branch_id' => $branches[$slug],
                ]);
            }
        }
    }
};
