<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->safeCreateIndex('programs', ['is_active', 'sort_order'], 'programs_active_sort_index');
        $this->safeCreateIndex('coaches', ['is_active', 'sort_order'], 'coaches_active_sort_index');
        $this->safeCreateIndex('testimonials', ['status', 'is_featured', 'published_at'], 'testimonials_status_featured_published_index');
        $this->safeCreateIndex('faqs', ['is_published', 'sort_order'], 'faqs_published_sort_index');
        $this->safeCreateIndex('posts', ['status', 'published_at', 'category_id'], 'posts_status_published_category_index');

        if (Schema::hasColumn('students', 'status')) {
            $this->safeCreateIndex('students', ['branch', 'status'], 'students_branch_status_index');
        }
        if (Schema::hasColumn('attendances', 'status')) {
            $this->safeCreateIndex('attendances', ['date', 'status'], 'attendances_date_status_index');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->safeDropIndex('programs', 'programs_active_sort_index');
        $this->safeDropIndex('coaches', 'coaches_active_sort_index');
        $this->safeDropIndex('testimonials', 'testimonials_status_featured_published_index');
        $this->safeDropIndex('faqs', 'faqs_published_sort_index');
        $this->safeDropIndex('posts', 'posts_status_published_category_index');
        if (Schema::hasColumn('students', 'status')) {
            $this->safeDropIndex('students', 'students_branch_status_index');
        }
        if (Schema::hasColumn('attendances', 'status')) {
            $this->safeDropIndex('attendances', 'attendances_date_status_index');
        }
    }

    private function safeCreateIndex(string $table, array $columns, string $indexName): void
    {
        try {
            Schema::table($table, function (Blueprint $table) use ($columns, $indexName) {
                $table->index($columns, $indexName);
            });
        } catch (QueryException $e) {
            // 1061 = Duplicate key name
            if ($e->errorInfo[1] !== 1061) {
                throw $e;
            }
        }
    }

    private function safeDropIndex(string $table, string $indexName): void
    {
        try {
            Schema::table($table, function (Blueprint $table) use ($indexName) {
                $table->dropIndex($indexName);
            });
        } catch (QueryException $e) {
            // 1091 = Can't DROP 'index'; check that it exists
            if ($e->errorInfo[1] !== 1091) {
                throw $e;
            }
        }
    }
};
