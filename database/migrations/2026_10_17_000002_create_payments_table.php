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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('payment_type_id')
                ->nullable()
                ->constrained('payment_types')
                ->nullOnDelete();

            $table->string('invoice_number')->unique();

            // Periode pembayaran, contoh: "2026-10" untuk iuran bulanan
            $table->string('period')->nullable();

            $table->string('description')->nullable();

            $table->decimal('amount', 12, 2)->default(0);

            // unpaid, pending, paid, expired, failed, cancelled
            $table->string('status')->default('unpaid');

            // cash, transfer, qris, gateway
            $table->string('payment_method')->nullable();

            $table->date('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();

            // Bukti bayar (upload manual) dan bukti transfer
            $table->string('proof_file')->nullable();

            // Persiapan payment gateway
            $table->string('gateway_name')->nullable();
            $table->string('gateway_reference')->nullable();
            $table->json('gateway_payload')->nullable();

            $table->text('notes')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_id', 'status']);
            $table->index('status');
            $table->index('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
