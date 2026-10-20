<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_schedules', function (Blueprint $table) {
            $table->foreignId('coach_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->after('coach_name');
            $table->index(['coach_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('training_schedules', function (Blueprint $table) {
            $table->dropForeign(['coach_id']);
            $table->dropIndex(['coach_id', 'is_active']);
            $table->dropColumn('coach_id');
        });
    }
};
