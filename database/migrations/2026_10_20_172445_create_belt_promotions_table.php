<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('belt_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('examined_by')->constrained('users');
            $table->date('exam_date');
            $table->string('current_belt');
            $table->string('target_belt');
            $table->enum('result', ['lulus', 'tidak_lulus', 'ditunda'])->default('ditunda');
            $table->text('notes')->nullable();
            $table->text('examiner_notes')->nullable();
            $table->date('certificate_issued_at')->nullable();
            $table->string('certificate_number')->nullable()->unique();
            $table->timestamps();

            $table->index(['student_id', 'exam_date']);
            $table->index(['exam_date', 'result']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('belt_promotions');
    }
};
