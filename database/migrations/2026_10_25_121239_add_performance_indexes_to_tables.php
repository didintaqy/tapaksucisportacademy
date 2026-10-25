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
        // programs
        Schema::table('programs', function (Blueprint $table) {
            $table->index(['is_active', 'sort_order'], 'programs_active_sort_index');
        });

        // coaches
        Schema::table('coaches', function (Blueprint $table) {
            $table->index(['is_active', 'sort_order'], 'coaches_active_sort_index');
        });

        // testimonials
        Schema::table('testimonials', function (Blueprint $table) {
            $table->index(['status', 'is_featured', 'published_at'], 'testimonials_status_featured_published_index');
        });

        // faqs
        Schema::table('faqs', function (Blueprint $table) {
            $table->index(['is_published', 'sort_order'], 'faqs_published_sort_index');
        });

        // posts
        Schema::table('posts', function (Blueprint $table) {
            $table->index(['status', 'published_at', 'category_id'], 'posts_status_published_category_index');
        });

        // students (common filters) - only if status column exists
        if (Schema::hasColumn('students', 'status')) {
            Schema::table('students', function (Blueprint $table) {
                $table->index(['branch', 'status'], 'students_branch_status_index');
            });
        }

        // attendances already has unique constraint, but add index for date lookups - only if status column exists
        if (Schema::hasColumn('attendances', 'status')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->index(['date', 'status'], 'attendances_date_status_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropIndex('programs_active_sort_index');
        });
        Schema::table('coaches', function (Blueprint $table) {
            $table->dropIndex('coaches_active_sort_index');
        });
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropIndex('testimonials_status_featured_published_index');
        });
        Schema::table('faqs', function (Blueprint $table) {
            $table->dropIndex('faqs_published_sort_index');
        });
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('posts_status_published_category_index');
        });
        if (Schema::hasColumn('students', 'status')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropIndex('students_branch_status_index');
            });
        }
        if (Schema::hasColumn('attendances', 'status')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropIndex('attendances_date_status_index');
            });
        }
    }
};
