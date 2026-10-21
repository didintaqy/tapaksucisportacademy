<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('program_id')->nullable()->after('branch')->constrained()->nullOnDelete();
        });

        Schema::table('training_schedules', function (Blueprint $table) {
            $table->foreignId('program_id')->nullable()->after('class_name')->constrained()->nullOnDelete();
        });

        Schema::table('student_registrations', function (Blueprint $table) {
            $table->foreignId('program_id')->nullable()->after('branch')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropConstrainedForeignId('program_id');
        });

        Schema::table('training_schedules', function (Blueprint $table) {
            $table->dropConstrainedForeignId('program_id');
        });

        Schema::table('student_registrations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('program_id');
        });
    }
};
