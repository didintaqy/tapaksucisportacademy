<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fase C3 — Inventaris (seragam, alat, perlengkapan).
 *
 * `items.stock` adalah saldo terkini; `stock_movements` menyimpan riwayat
 * masuk/keluar/penyesuaian.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 40)->unique();
            $table->string('name');
            $table->string('category', 40)->default('lain'); // seragam | alat | lain
            $table->string('unit', 20)->default('pcs');
            $table->decimal('price', 12, 2)->default(0);
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('min_stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category', 'is_active'], 'items_category_active_index');
        });

        Schema::create('stock_movements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->string('type', 20); // in | out | adjustment
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 12, 2)->nullable();
            $table->string('reference', 120)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('moved_at');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['item_id', 'moved_at'], 'stock_movements_item_date_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('items');
    }
};
