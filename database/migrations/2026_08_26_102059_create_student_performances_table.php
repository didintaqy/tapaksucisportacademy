<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_performances', function (Blueprint $table) {
            $table->id();

            // Relasi ke students
            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            // Heart Rate
            $table->unsignedInteger('resting_heart_rate')->nullable();
            $table->unsignedInteger('max_heart_rate')->nullable();
            $table->unsignedInteger('training_heart_rate')->nullable();

            // Anthropometry
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('weight', 5, 2)->nullable();

            // Flexibility
            $table->decimal('sit_and_reach', 5, 2)->nullable();

            // Muscular Endurance
            $table->unsignedInteger('push_up_1_min')->nullable();
            $table->unsignedInteger('sit_up_2_min')->nullable();

            // Core Stability
            $table->string('core_stability_level')->nullable();

            // Triple Hop Test
            $table->decimal('triple_hop_right', 6, 2)->nullable();
            $table->decimal('triple_hop_left', 6, 2)->nullable();

            // Illinois Agility Test
            $table->decimal('illinois_agility', 6, 2)->nullable();

            // RAST Test
            $table->decimal('rast_average_power', 10, 2)->nullable();
            $table->decimal('rast_fatigue_index', 10, 2)->nullable();

            // Beep Test
            $table->decimal('beep_test_vo2max', 6, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_performances');
    }
};
