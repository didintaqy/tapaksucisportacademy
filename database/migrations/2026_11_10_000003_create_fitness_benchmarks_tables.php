<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fitness_components', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->string('name');
            $table->decimal('weight', 5, 2)->default(20);
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('fitness_test_items', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->string('key', 64)->unique();
            $table->string('name');
            $table->foreignId('fitness_component_id')
                ->constrained('fitness_components')
                ->cascadeOnDelete();
            $table->string('unit', 32)->nullable();
            $table->string('direction', 16)->default('HIGHER');
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('fitness_benchmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fitness_test_item_id')
                ->constrained('fitness_test_items')
                ->cascadeOnDelete();
            $table->string('gender', 16)->default('Semua');
            $table->unsignedSmallInteger('age_min')->default(5);
            $table->unsignedSmallInteger('age_max')->default(40);
            $table->decimal('target_value', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['fitness_test_item_id', 'gender', 'is_active']);
        });

        Schema::create('score_categories', function (Blueprint $table) {
            $table->id();
            $table->decimal('min_score', 5, 2);
            $table->decimal('max_score', 5, 2);
            $table->string('label');
            $table->string('description')->nullable();
            $table->string('color', 16)->default('#27924F');
            $table->timestamps();
        });

        Schema::table('user_performances', function (Blueprint $table) {
            $table->date('test_date')->nullable()->after('student_id');
            $table->foreignId('coach_id')
                ->nullable()
                ->after('test_date')
                ->constrained('users')
                ->nullOnDelete();
            $table->text('notes')->nullable()->after('beep_test_vo2max');
            $table->json('custom_results')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('user_performances', function (Blueprint $table) {
            $table->dropConstrainedForeignId('coach_id');
            $table->dropColumn(['test_date', 'notes', 'custom_results']);
        });

        Schema::dropIfExists('score_categories');
        Schema::dropIfExists('fitness_benchmarks');
        Schema::dropIfExists('fitness_test_items');
        Schema::dropIfExists('fitness_components');
    }
};
