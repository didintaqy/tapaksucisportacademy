<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fase B4 — Log notifikasi keluar (email/WhatsApp) untuk audit dan retry.
 *
 * Belum ada pengiriman WhatsApp; tabel ini disiapkan agar channel baru bisa
 * dicatat tanpa mengubah struktur lagi.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('channel', 20);            // email | whatsapp | sms
            $table->string('recipient', 180);
            $table->string('template', 80)->nullable();
            $table->string('subject_type', 120)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('status', 20)->default('queued'); // queued | sent | failed
            $table->string('provider_ref', 120)->nullable();
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'channel', 'created_at'], 'notification_logs_status_channel_index');
            $table->index(['subject_type', 'subject_id'], 'notification_logs_subject_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
