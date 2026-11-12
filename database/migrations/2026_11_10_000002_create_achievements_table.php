<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')
                ->nullable()
                ->constrained('students')
                ->nullOnDelete();
            $table->string('competition_name');
            $table->string('level', 32)->default('kota');
            $table->date('event_date');
            $table->string('location')->nullable();
            $table->string('organizer')->nullable();
            $table->string('category')->nullable();
            $table->string('weight_class')->nullable();
            $table->string('medal', 32)->default('emas');
            $table->string('certificate_file')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['is_published', 'event_date']);
            $table->index(['student_id', 'event_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
